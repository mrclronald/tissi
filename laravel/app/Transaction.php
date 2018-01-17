<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'plate_number',
        'type',
        'gvw',
        'axle_load_1',
        'axle_load_2',
        'axle_load_3',
        'axle_load_4',
        'axle_load_5',
        'axle_load_6',
        'axle_load_7',
        'axle_load_8',
        'area_of_operation',
        'affiliation',
        'date',
        'created_at'
    ];

    public function upload()
    {
        return $this->belongsTo(Uploads::class);
    }
}
