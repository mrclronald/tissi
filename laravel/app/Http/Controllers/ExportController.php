<?php

namespace App\Http\Controllers;

use App\TransactionListImport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{

    private $ltoSummaryHeaders = [
        'transaction_number' => 'NO.', // numbering only
        'vehicle_class_id' => 'MV PLATE NO.',
        'vehicle_class_name_private' => 'MV TYPE (Private/ For-Hire/ Gov.t\' / Diplomat)', // Manual
        'vehicle_class_name' => 'MV TYPE (Bus/ Jeepney/ Van/ Truck etc.)',
        'trade_name' => 'TRADE NAME', // Manual
        'year_model' => 'YEAR MODEL', // Manual
        'axle_load' => 'GVW/Axle Load', // computed, it's either axle or gvw or axle / gvw
        'remarks' => 'REMARKS (Passed or Failed)', // computed
        'action' => 'ACTION TAKEN CONFISCATED ITEMS/ IMPOUNDED MV',
        'gvw_axle' => ' GVW / AXLE'
    ];

    private $weeklySummaryHeaders = [

    ];

    private $monthlySummaryHeaders = [

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

    public function exportSheet(TransactionListImport $import)
    {
        $this->filename = public_path('transactions') . '/' . Input::post('filename');
        $results = $import->takeRows(100)->toArray();

        $data = $this->prepareData($results);

        $summaryReport = $this->getSummaryReport($data, Input::post('template'));

        $summaryReport->export('xls');
    }

    private function getSummaryReport($data, $template = 'lto')
    {
        if ($template === 'weekly') {
            return $this->getWeeklySummaryReport($data);
        } else if ($template === 'monthly') {
            return $this->getMonthlySummaryReport($data);
        }

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
                $sheet->fromArray($data, null, 'A7');

            }
        );
    }

    private function getWeeklySummaryReport($data)
    {
        return Excel::create(
            'Weekly Summary Report', function ($excel) use ($data) {

                // Set the title
                $excel->setTitle('WEEKLY SUMMARY REPORT OF ANTI-OVERLOADING OPERATION');

                // Chain the setters
                $excel->setCreator('TISSI')->setCompany('TISSI');

                // Call them separately
                $excel->setDescription('A demonstration to change the file properties');

                $excel->sheet(
                    '6AM-2PM MAL', function ($sheet) use ($data) {
                        $sheet->fromArray($data);
                    }
                );

            }
        );
    }

    private function getMonthlySummaryReport($data)
    {
        return Excel::create(
            'Monthly Summary Report', function ($excel) use ($data) {

                // Set the title
                $excel->setTitle('SUMMARY REPORT OF ANTI-OVERLOADING OPERATION');

                // Chain the setters
                $excel->setCreator('TISSI')->setCompany('TISSI');

                // Call them separately
                $excel->setDescription('A demonstration to change the file properties');

                $excel->sheet(
                    '6AM-2PM MAL', function ($sheet) use ($data) {
                        $sheet->fromArray($data);
                    }
                );

            }
        );
    }
}