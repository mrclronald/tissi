<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'plate_number',
        'type',
        'gvw',
        'axle_load',
        'area_of_operation',
        'affiliation',
        'date',
        'created_at'
    ];
}
