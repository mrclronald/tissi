<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class ExportController extends Controller
{

    private $ltoSummaryHeaders = [
        'transaction_number' => 'NO.', // numbering only
        'plate_number' => 'MV PLATE NO.',
        'vehicle_class_name_private' => 'MV TYPE (Private/ For-Hire/ Gov.t\' / Diplomat)', // Manual
        'type' => 'MV TYPE (Bus/ Jeepney/ Van/ Truck etc.)',
        'trade_name' => 'TRADE NAME', // Manual
        'blank' => '',
        'year_model' => 'YEAR MODEL', // Manual
        'axle_load' => 'GVW_Axle_Load', // computed, it's either axle or gvw or axle / gvw
        'remarks' => 'REMARKS (Passed or Failed)', // computed
        'action' => 'ACTION TAKEN CONFISCATED ITEMS/ IMPOUNDED MV',
        'gvw_or_axle' => ' GVW / AXLE' //computed
    ];

    private $maxAllowableGvw = [
        '1-1' => '18000',
        '1-2' => '33300',
        '1-3' => '35600',
        '11-1' => '34000',
        '11-2' => '40600',
        '11-3' => '41000',
        '12-1' => '39700',
        '12-2' => '41500',
        '12-3' => '42000',
        '11-11' => '39700',
        '11-12' => '43500',
        '12-11' => '43500',
        '12-12' => '45000',
    ];

    private $maxAllowableGvwExempted = [
        '12-2',
        '12-3'
    ];

    private $templates = [
        'lto' => 'LTO Summary Report',
        'weekly' => 'Weekly Summary Report',
        'monthly' => 'Monthly Summary Report'
    ];

    private $areaOfOperations = [
        'pacheco' =>'R-10 PACHECO',
        'lingahan' =>'R-11 LINGAHAN'
    ];

    private $teamOrAffiliations = [
        'ncr' =>'DPWH-NCR-NORTH MANILA ENGINEERING DISTRICT',
        'region _1' =>'DPWH-REGION 1-NORTH VISAYA ENGINEERING DISTRICT',
        'region _2' =>'DPWH-REGION 2-NORTH MINDANAO ENGINEERING DISTRICT'
    ];

    private $axleLoadKeys = [
        'axle_load_1',
        'axle_load_2',
        'axle_load_3',
        'axle_load_4',
        'axle_load_5',
        'axle_load_6',
        'axle_load_7',
        'axle_load_8'
    ];

    private $failedRows = [];

    private $failedExemptedRows = [];

    private $filename;

    private $count = 1;


    public function index()
    {
        $directories = File::files(public_path('transactions'));

        return view('export', [
            'directories' => $directories,
            'templates' => $this->templates,
            'areaOfOperations' => $this->areaOfOperations,
            'teamOrAffiliations' => $this->teamOrAffiliations,
        ]);
    }

    public function exportFromDb(Request $request)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $areaOfOperation = $request->get('areaOfOperation');
        $teamOrAffiliation = $request->get('teamOrAffiliation');

        $transactions = Transaction::whereBetween('date', [$fromDate, $toDate])->get();

        $chunkedResults = array_chunk($transactions->toArray(), 25);

        $data = [];
        foreach ($chunkedResults as $results) {
            $data[] = $this->prepareData($results);
        }
        $summaryReport = $this->getSummaryReport($data);

        $summaryReport->export('xls');
    }

    private function getSummaryReport($data, $template = 'lto')
    {
        return $this->getLtoSummaryReport($data);
    }

    private function highlightRow($cells, $color)
    {
        $cells->setBackground($color);
    }

    private function applyMappers($transactionsData, $mappers = [])
    {
        $newData = [];
        foreach ($transactionsData as $key => $data) {
            // We decalared this variable here so we can use it in gvw_or_axle in the loop
            $overWeightAxles = [];
            $newData[$key][$mappers['transaction_number']] = $this->count;

            foreach ($mappers as $k => $mapper) {

                if ($k === 'transaction_number') {
                    continue;
                }
                
                // axle_load
                if ($k === 'axle_load' && ! empty($data['type'])) {
                    $value = $data['gvw'];

                    foreach ($this->axleLoadKeys as $axleLoadKey) {
                        if ($data[$axleLoadKey] > 13500) {
                            $overWeightAxles[] = $data[$axleLoadKey];
                        }
                    }
            
                    $newData[$key][$mapper] = $value
                        . (count($overWeightAxles) ? '/ (' : '')
                        . implode(',', $overWeightAxles)
                        . (count($overWeightAxles) ? ')' : '');
                    continue;
                }

                // gvw_or_axle
                if ($k === 'gvw_or_axle' && ! empty($data['type'])) {

                    $isGvwOverWeight = $data['gvw'] > $this->maxAllowableGvw[$data['type']];

                    if ($isGvwOverWeight && count($overWeightAxles)) {
                        // If both is/has overweight
                        $value = 'BOTH';
                    } else {
                        // if one of them is/has not overweight
                        $value = count($overWeightAxles)
                            ? 'AXLE'
                            : ($isGvwOverWeight ? 'GVW' : '');
                    }

                    // If not empty, assign to failed array
                    if (! empty($value)) {
                        if (in_array($data['type'], ['12-2', '12-3'])) {
                            $this->failedExemptedRows[] = $this->count;
                        } else {
                            $this->failedRows[] = $this->count;
                        }
                    }
                    
                    $newData[$key][$mapper] = $value;
                    $newData[$key][$mappers['remarks']] = ! empty($value) ? 'FAILED' : 'PASSED';
                    continue;
                }

                $newData[$key][$mapper] = isset($data[$k])
                    ? $data[$k]
                    : '';
            }

            $this->count++;
        }

        return $newData;
    }

    private function prepareData($rawData)
    {
        $preparedData = $this->applyMappers($rawData, $this->ltoSummaryHeaders);

        return $preparedData;
    }

    private function getLtoSummaryReport($data)
    {
        return Excel::create('test', function($excel) use($data) {

            // Set the title
            $excel->setTitle('Our new awesome title');

            // Chain the setters
            $excel->setCreator('Maatwebsite')
            ->setCompany('Maatwebsite');

            // Call them separately
            $excel->setDescription('A demonstration to change the file properties');

            $excel->sheet('First sheet', function($sheet) use($data) {
                $sheet->loadView('demo', array('data' => $data));
            });

        })->export('xlsx');

        return Excel::create('LTO Summary Report', function ($excel) use ($data) {
                // Set the title
            $excel->setTitle('SUMMARY REPORT OF ANTI-OVERLOADING OPERATION');

            $excel->sheet('test', function ($sheet) use ($data) {

                // Font family
                $sheet->setFontFamily('Times New Roman');

                // Font size
                $sheet->setFontSize(11);

                // Font bold
                $sheet->setFontBold(false);


                $sheet->setAllBorders(PHPExcel_Style_Border::BORDER_THIN);

                $sheet->setBorder('A3:K3', PHPExcel_Style_Border::BORDER_NONE);

                foreach ($this->failedRows as $failedRow) {
                    $cellNumber = $failedRow + 6;
                    $sheet->getStyle("A$cellNumber:K$cellNumber")->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('ff0000');
                }

                foreach ($this->failedExemptedRows as $failedRow) {
                    $cellNumber = $failedRow + 6;
                    $sheet->getStyle("A$cellNumber:K$cellNumber")->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('0080ff');
                }

                $sheet->setMergeColumn(array(
                    'columns' => array('A','B','C','D','E', 'F', 'G', 'H', 'I', 'J', 'K'),
                    'rows' => array(
                        array(4,5,6),
                    )
                ));
                $sheet->fromArray($data, null, 'A1', false, false);

                $start = 7;

                foreach ($data as $datum) {
                    // Add header
                    array_unshift($datum, $this->ltoSummaryHeaders);

                    // Blank
                    array_unshift($datum, ['blank']);

                    // informations
                    array_unshift($datum, ['informations']);

                    // title
                    array_unshift($datum, ['title']);

                    $cell = 'A' . $start;
                    $sheet->fromArray($datum, null, $cell);
                    $start += 35;
                }
            });
        });
    }
}
