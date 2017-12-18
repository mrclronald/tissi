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

    private $summaryHeaders = [
        'date' => 'DATE',
        'name' => 'NAME OF OWNER',
        'address' => 'ADDRESS',
        'trade_name' => 'TRADE NAME',
        'plate_number' => 'PLATE NO.',
        'type' => 'CODE NO./VEHICLE TYPE',
        'gvw_as_weighed' => 'GVW AS WEIGHED',
        'excess_load_axle' => '13,500/AXLE',
        'excess_load_gvw' => 'GVW',
        'excess_load_both' => 'BOTH AXLE-GVW',
        'officer' => 'APPREHENDING OFFICER',
        'confiscated_item' => 'CONFISCATED ITEM'
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
        0 => 'ALL',
        'MCARTHUR' => 'R-10 PACHECO'
    ];

    private $teamOrAffiliations = [
        'ALL',
        0 => 'DPWH-NCR-NORTH MANILA ENGINEERING DISTRICT'
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
        $this->dateSelected = "$fromDate - $toDate";
        $areaOfOperation = $request->get('areaOfOperation');
        $teamOrAffiliation = $request->get('teamOrAffiliation');
        $template = $request->get('template', 'lto');

        $transactions = Transaction::whereBetween('date', [$fromDate, $toDate])->get();

        if ($areaOfOperation) {
            $transactions->where('area_of_operation', $areaOfOperation);
        }

        if ($teamOrAffiliation) {
            $transactions->where('affiliation', $teamOrAffiliation);
        }

        $report = $this->getReport($transactions->toArray(), $areaOfOperation, $teamOrAffiliation, $template);

        $report->export('xls');
    }

    private function getReport($rawTransactionsData, $areaOfOperation, $teamOrAffiliation, $template)
    {
        switch ($template) {
            case 'lto':
                // we need chuncked results for lto for pagination
                $chunkedResults = array_chunk($rawTransactionsData, 25);
                $data = [];
                foreach ($chunkedResults as $results) {
                    $data[] = $this->applyLtoMappers($results, $this->ltoSummaryHeaders);
                }

                $summaryReport = $this->getLtoSummaryReport($data, $areaOfOperation, $teamOrAffiliation);
                break;
            default:
                $data = $this->applyMappers($rawTransactionsData, $this->summaryHeaders);
                $summaryReport = $this->getSummaryReport($data, $areaOfOperation, $teamOrAffiliation, $template);
                break;
        }

        return $summaryReport;
    }

    private function getExcess($a, $b)
    {
        if ($a < $b) {
            return '';
        }

        return $a - $b;
    }

    private function applyMappers($transactionsData, $mappers)
    {
        $newData = [];

        foreach ($transactionsData as $key => $data) {

            $hasAxle = false;
            $hasGvw = false;
            $axles = [];
            foreach ($mappers as $k => $mapper) {

                if ($k === 'type') {
                    $newData[$key][$mapper] = 'TRUCK ' . $data[$k];
                    continue;
                }

                if ($k === 'gvw_as_weighed') {
                    $value = '';

                    foreach ($this->axleLoadKeys as $axleLoadKey) {
                        $axles[] = $data[$axleLoadKey];
                    }

                    if (count($axles)) {
                        $hasAxle = true;
                        $value .= '13500 ';
                    }


                    if (!empty($data['gvw'])) {
                        $hasGvw = true;
                        $value .= $this->maxAllowableGvw[$data['type']];
                    }

                    $newData[$key][$mapper] = $value;
                    continue;
                }

                if ($k === 'excess_load_axle') {
                    $newData[$key][$mapper] = $hasAxle && !$hasGvw ? $this->getExcess(array_sum($axles), 13500) : '';
                    continue;
                }

                if ($k === 'excess_load_gvw') {
                    $newData[$key][$mapper] = !$hasAxle && $hasGvw
                        ? $this->getExcess($data['gvw'] , $this->maxAllowableGvw[$data['type']])
                        : '';
                    continue;
                }

                if ($k === 'excess_load_both') {
                    $newData[$key][$mapper] = $hasAxle && $hasGvw
                        ? $this->getExcess(array_sum($axles), 13500)
                            . ' ' .
                            $this->getExcess($data['gvw'] , $this->maxAllowableGvw[$data['type']])
                        : '';
                    continue;
                }

                if ($k === 'confiscated_item') {
                    $newData[$key][$mapper] = !empty(trim($data['plate_number']))
                        ? '1 PLATE ' . $data['plate_number']
                        : '';
                    continue;
                }

                $newData[$key][$mapper] = isset($data[$k])
                    ? $data[$k]
                    : '';
            }
        }

        return $newData;
    }

    private function applyLtoMappers($transactionsData, $mappers)
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
                if ($k === 'axle_load' && !empty($data['type'])) {
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
                if ($k === 'gvw_or_axle' && !empty($data['type'])) {

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
                    $exempted = false;
                    if (!empty($value)) {
                        if (in_array($data['type'], ['12-2', '12-3'])) {
                            $exempted = true;
                            $this->failedExemptedRows[] = $this->count;
                        } else {
                            $this->failedRows[] = $this->count;
                        }
                    }

                    $newData[$key][$mapper] = $value;
                    $newData[$key][$mappers['remarks']] = !empty($value) && !$exempted ? 'FAILED' : 'PASSED';
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

    private function getSummaryReport($data, $areaOfOperation, $teamOrAffiliation, $template)
    {
        return Excel::create($this->templates[$template], function ($excel) use ($data, $areaOfOperation, $teamOrAffiliation, $template) {

            // Set the title
            $excel->setTitle('Our new awesome title');

            // Chain the setters
            $excel->setCreator('Maatwebsite')->setCompany('Maatwebsite');

            // Call them separately
            $excel->setDescription('A demonstration to change the file properties');

            $excel->sheet('sheet1', function ($sheet) use ($data, $areaOfOperation, $teamOrAffiliation, $template) {

                $sheet->loadView("$template-summary-report", [
                    'data' => $data,
                    'headers' => $this->ltoSummaryHeaders,
                    'date' => $this->dateSelected,
                    'areaOfOperation' => $areaOfOperation,
                    'teamOrAffiliaion' => $this->teamOrAffiliations[$teamOrAffiliation],
                    'failedRows' => $this->failedRows,
                    'failedExemptedRows' => $this->failedExemptedRows,
                    'totalMVWeighed' => $this->count,
                    'totalMVPassed' => $this->count - count($this->failedRows),
                    'totalMVFailed' => count($this->failedRows),
                ]);

                $sheet->setHeight([
                    3 => 20,
                    4 => 20
                ]);

                $sheet->setWidth([
                    'A' => 6,
                    'B' => 12,
                    'C' => 14,
                    'D' => 13.4,
                    'E' => 19.71,
                    'F' => 8.57,
                    'G' => 9,
                    'H' => 12.29,
                    'I' => 11.71,
                    'J' => 19,
                    'K' => 12.29,
                ]);

                $sheet->setStyle([
                    'alignment' => [
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'wrap' => true
                    ],
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 11,
                    ]
                ]);
            });

        });
    }

    private function getLtoSummaryReport($data, $areaOfOperation, $teamOrAffiliation)
    {
        return Excel::create('LTO Summary Report', function ($excel) use ($data, $areaOfOperation, $teamOrAffiliation) {

            // Set the title
            $excel->setTitle('Our new awesome title');

            // Chain the setters
            $excel->setCreator('Maatwebsite')->setCompany('Maatwebsite');

            // Call them separately
            $excel->setDescription('A demonstration to change the file properties');

            $excel->sheet('sheet1', function ($sheet) use ($data, $areaOfOperation, $teamOrAffiliation) {

                $sheet->loadView('lto-summary-report', [
                    'data' => $data,
                    'headers' => $this->ltoSummaryHeaders,
                    'date' => $this->dateSelected,
                    'areaOfOperation' => $areaOfOperation,
                    'teamOrAffiliaion' => $this->teamOrAffiliations[$teamOrAffiliation],
                    'failedRows' => $this->failedRows,
                    'failedExemptedRows' => $this->failedExemptedRows,
                    'totalMVWeighed' => $this->count,
                    'totalMVPassed' => $this->count - count($this->failedRows),
                    'totalMVFailed' => count($this->failedRows),
                ]);

                $sheet->getStyle('G2')->getAlignment()->setShrinkToFit(true);

                $sheet->setWidth([
                    'A' => 6,
                    'B' => 12,
                    'C' => 14,
                    'D' => 13.4,
                    'E' => 19.71,
                    'F' => 8.57,
                    'G' => 9,
                    'H' => 12.29,
                    'I' => 11.71,
                    'J' => 19,
                    'K' => 12.29,
                ]);

                $sheet->setStyle([
                    'font' => [
                        'name' => 'Times New Roman',
                        'size' => 11,
                    ]
                ]);

            });

        });
    }
}
