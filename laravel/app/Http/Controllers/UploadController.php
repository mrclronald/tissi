<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UploadController extends Controller
{
    /**
     * '{from_excel}' => '{from_db}'
     *
     * @var array
     */
    private $ltoSummaryHeaders = [
        'upload_name' => 'upload_name',
        'vehicle_class_id' => 'plate_number',
        'vehicle_class_name' => 'type',
        'vehicle_weight' => 'gvw',
        'axle_1_weight' => 'axle_load_1',
        'axle_2_weight' => 'axle_load_2',
        'axle_3_weight' => 'axle_load_3',
        'axle_4_weight' => 'axle_load_4',
        'axle_5_weight' => 'axle_load_5',
        'axle_6_weight' => 'axle_load_6',
        'axle_7_weight' => 'axle_load_7',
        'axle_8_weight' => 'axle_load_8',
        'site_name' => 'area_of_operation',
        'lane_id' => 'affiliation',
        'date' => 'date'
    ];

    public function index()
    {
        return view('upload');
    }

    public function uploadSheet(Request $request)
    {
        $fileSheet = $request->file('transactions');
        $fileSheetName = $this->process($fileSheet);

        return public_path('transactions') . '/' . $fileSheetName;
    }

    private function process($fileSheet)
    {
        try {
            DB::beginTransaction();
            // Create filename
            $fileSheetName = time() . '_' . $fileSheet->getClientOriginalName();

            // Create path public/transactions/
//        $fileSheet->move(public_path('transactions'), $fileSheet->getClientOriginalName());

            $data = Excel::load($fileSheet->getRealPath(), function ($reader) {})->get(
                array_keys($this->ltoSummaryHeaders)
            );

            $dataImported = [];
            if (!empty($data) && $data->count()) {

                $upload = new Uploads;
                $upload->name = $fileSheetName ;
                $upload->save();
                /**
                 * $rows[0] => array:14 [
                 *       "site_name" => null
                 *       "lane_id" => 0.0
                 *       "date" => Carbon {#394871 …3}
                 *       "vehicle_weight" => "------"
                 *       "axle_1_weight" => "------"
                 *       "axle_2_weight" => "------"
                 *       "axle_3_weight" => "------"
                 *       "axle_4_weight" => "------"
                 *       "axle_5_weight" => "------"
                 *       "axle_6_weight" => "------"
                 *       "axle_7_weight" => null
                 *       "axle_8_weight" => null
                 *       "vehicle_class_id" => null
                 *       "vehicle_class_name" => null
                 *   ]
                 */
                $rows = $data->toArray();

                foreach ($rows as $key => $row) {
                    if (empty((float)$row['vehicle_weight'])
                        && empty((float)$row['axle_1_weight'])
                        && empty((float)$row['axle_2_weight'])
                        && empty((float)$row['axle_3_weight'])
                        && empty((float)$row['axle_4_weight'])
                        && empty((float)$row['axle_5_weight'])
                        && empty((float)$row['axle_6_weight'])
                        && empty((float)$row['axle_7_weight'])
                        && empty((float)$row['axle_8_weight'])
                    ) {
                        continue;
                    }
                    // We are inside a  row
                    // $row = ["site_name" => null, "lane_id" => 0.0 =...]
                    $dataImported[$key]['upload_id'] = $upload->id;
                    foreach ($this->ltoSummaryHeaders as $k => $mapper) {

                        // eg. $k => 'vehicle_class_id'
                        // eg. $mapper => 'plate_number'

                        if (in_array($k, [
                            'axle_1_weight',
                            'axle_2_weight',
                            'axle_3_weight',
                            'axle_4_weight',
                            'axle_5_weight',
                            'axle_6_weight',
                            'axle_7_weight',
                            'axle_8_weight',
                            'vehicle_weight'
                        ])) {
                            $dataImported[$key][$mapper] = (float)$row[$k];
                            continue;
                        }

                        // Check if there is no existing
                        if (! isset($row[$k])) {
                            $dataImported[$key][$mapper] = '';
                            continue;
                        }

                        if ($k === 'date') {
                            $dataImported[$key][$mapper] = date("Y-m-d h:i:s", strtotime($row[$k]));
                            continue;
                        }

                        if ($k === 'vehicle_class_name') {
                            $dataImported[$key][$mapper] = date("j-n", strtotime($row[$k]));
                            continue;
                        }

                        $dataImported[$key][$mapper] = $row[$k];
                    }
                }
            }

            if (! count($dataImported)) {
                throw new \Exception('No imported data');
            }

            $upload->transactions()->createMany($dataImported);
//            $this->saveInDb($upload, $dataImported);
            info('Done');
            DB::commit();
        } catch (\Exception $e) {
            info('Rolback');
            DB::rollback();
        }
    }

    private function saveInDb(Uploads $upload, $dataImported)
    {
        $upload->transactions()->createMany($dataImported);
    }

}