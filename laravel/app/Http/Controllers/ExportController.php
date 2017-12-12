<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\TransactionListImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

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
        'axle_load' => 'GVW / Axle Load', // computed, it's either axle or gvw or axle / gvw
        'remarks' => 'REMARKS (Passed or Failed)', // computed
        'action' => 'ACTION TAKEN CONFISCATED ITEMS/ IMPOUNDED MV',
        'gvw_or_axle' => ' GVW / AXLE'
    ];

    private $templates = [
        'lto' => 'LTO Summary Report',
        'weekly' => 'Weekly Summary Report',
        'monthly' => 'Monthly Summary Report'
    ];

    private $filename;


    public function index()
    {
        $directories = File::files(public_path('transactions'));

        return view('export', ['directories' => $directories, 'templates' => $this->templates]);
    }

    public function exportFromDb(Request $request)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

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

    private function applyMappers($rawData, $mappers = [])
    {
        $newData = [];
        $maxAllowableGvw = [
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

        foreach ($rawData as $key => $data) {
            foreach ($mappers as $k => $mapper) {
                // axle_load
                if ($k === 'axle_load') {
                    // you can use axle_load_1, axle_load_2, axle_load_3, ...
                    $value = 'test';
                    $newData[$key][$mapper] = $value;
                    continue;
                }                

                // gvw_or_axle
                if ($k === 'gvw_or_axle' && ! empty($data['type'])) {
                    // you can use axle_load_1, axle_load_2, axle_load_3, ...
                    $value = ($data['gvw'] > $maxAllowableGvw[$data['type']] ? 'GVW' : 'AXLE');
                    $newData[$key][$mapper] = $value;
                    continue;
                }

                $newData[$key][$mapper] = isset($data[$k])
                    ? $data[$k]
                    : '';
            }
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
        return Excel::load(public_path('lto-template.xlsx'), function ($excel) use ($data) {
                // Set the title
                $excel->setTitle('SUMMARY REPORT OF ANTI-OVERLOADING OPERATION');
                $sheet = $excel->setActiveSheetIndex(0);

                $start = 7;
                foreach ($data as $datum) {
                    $cell = 'A' . $start;
                    $sheet->fromArray($datum, null, $cell);
                    $start+=35;
                }
        });
    }
}