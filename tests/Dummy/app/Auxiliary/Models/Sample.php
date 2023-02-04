<?php namespace App\Auxiliary\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = ['value', 'collected_at'];

    protected $casts = [
        'collected_at' => 'datetime'
    ];
}