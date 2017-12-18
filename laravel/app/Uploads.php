<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uploads extends Model
{
    protected $fillable = [
        'created_at'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'upload_id');
    }
}
