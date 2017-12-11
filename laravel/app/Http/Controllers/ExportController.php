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

    // public function exportSheet(TransactionListImport $import)
    // {
    //     $this->filename = public_path('transactions') . '/' . Input::post('filename');
    //     $results = $import->takeRows(50)->toArray();
    //     $chunkedResults = array_chunk($results, 25);

    //     $data = [];
    //     foreach ($chunkedResults as $results) {
    //         $data[] = $this->prepareData($results);
    //     }

    //     $summaryReport = $this->getSummaryReport($data, Input::post('template'));

    //     $summaryReport->export('xls');
    // }

    private function getSummaryReport($data, $template = 'lto')
    {
        return $this->getLtoSummaryReport($data);
    }

    private function applyMappers($rawData, $mappers = [])
    {
        $newData = [];

        foreach ($rawData as $key => $data) {
            foreach ($mappers as $k => $mapper) {
                $newData[$key][$mapper] = isset($data[$k])
                    ? $data[$k]
                    : '';
            }
        }

        return $newData;
    }

    private function prepareData($rawData)
    {
        $mappers = $this->ltoSummaryHeaders;

        $preparedData = $this->applyMappers($rawData, $mappers);

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