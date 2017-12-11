<?php

use Illuminate\Database\Seeder;

class MaxAllowableGVWTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('max_allowable_gvw')->insert(['code' => '1-1' , 'description' => 'Truck with 2 axles (6 wheels)', 'value' => '18,000', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '1-2' , 'description' => 'Truck with tander rear axle 3 axles (10 wheels)', 'value' => '33,300', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '1-3' , 'description' => 'Truck with tridem rear axle 4 axles (14 wheels)', 'value' => '35,600', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '11-1' , 'description' => 'Truck-trailer with 2 axles at motor vehicle & 1 axle at trailer (10 wheels)', 'value' => '34,000', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '11-2' , 'description' => 'Truck -trailer with 2 axles at motor vehicle & 2 axles at trailer (14 wheeler)', 'value' => '40,600', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '11-3' , 'description' => 'Truck-trailer with 2 axles at motor vehicle & 3 axles at trailer (18 wheeler)', 'value' => '41,000', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '12-1' , 'description' => 'Truck semi-trailer with 3 axles at motor vehicle & 1 axle at trailer (14 wheeler)', 'value' => '39,700', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '12-2' , 'description' => 'Truck semi-trailer with 3 axles at motor vehicle & 2 axles at trailer (18 wheeler)', 'value' => '41,500', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '12-3' , 'description' => 'Truck semi-trailer with 3 axles at motor vehicle & 3 axles at trailer (22 wheels)', 'value' => '42,000', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '11-11' , 'description' => 'Truck-trailer with 2 axles at motor vehicle & 2 axles at trailer (14 wheels)', 'value' => '39,700', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '11-12' , 'description' => 'Truck-trailer with 2 axles at motor vehicle & 3 axles at trailer (18 wheeler)', 'value' => '43,500', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '12-11' , 'description' => 'Truck-trailer with 3 axles at motor vehicle & 2 axles at trauler (18 wheels)', 'value' => '43,500', ]);
        DB::table('max_allowable_gvw')->insert(['code' => '12-12' , 'description' => 'Truck-trailer with 3 axles at motor vehicle & 3 axles at trailer (22 wheels)', 'value' => '45,000', ]);
    }
}
