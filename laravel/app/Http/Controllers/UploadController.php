<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UploadController extends Controller
{
    private $ltoSummaryHeaders = [
        'vehicle_class_id' => 'plate_number',
        'vehicle_class_name' => 'type',
        'vehicle_weight' => 'gvw',
        'axle_1_weight' => 'axle_load',
        'site_name' => 'area_of_operation',
        'lane_id' => 'affiliation',
        'date' => 'date',
    ];

    public function index()
    {
        return view('upload');
    }

    public function uploadSheet(Request $request)
    {
        $fileSheet = $request->file('transactions');
        $fileSheetName = $this->doSomethingLikeUpload($fileSheet);

        return public_path('transactions') . '/' . $fileSheetName;
    }

    private function doSomethingLikeUpload($fileSheet)
    {
        $fileSheetName = time() . '.' . $fileSheet->getClientOriginalExtension();
        $path = public_path('transactions') . '/' . $fileSheetName;
        $data = Excel::load($fileSheet->getRealPath(), function ($reader) {})->get(array_keys($this->ltoSummaryHeaders));

        if (!empty($data) && $data->count()) {
            $data = $data->toArray();
            foreach ($data as $key => $data) {
                foreach ($this->ltoSummaryHeaders as $k => $mapper) {

                    if (! isset($data[$k])) {
                        $dataImported[$key][$mapper] = '';
                        continue;
                    }

                    if ($k === 'date') {
                        $dataImported[$key][$mapper] = date("Y-m-d h:i:s", strtotime($data[$k]));
                        continue;
                    }

                    if ($k === 'vehicle_class_name') {
                        $dataImported[$key][$mapper] = date("j-n", strtotime($data[$k]));
                        continue;
                    }

                    $dataImported[$key][$mapper] = $data[$k];
                }
            }
        }

        $this->saveInDb($dataImported);

        return $path;
    }

    private function saveInDb($dataImported)
    {
        Transaction::insert(array_slice($dataImported, 0, 100));
    }

}