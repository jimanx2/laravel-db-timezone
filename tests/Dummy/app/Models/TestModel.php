<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $fillable = [
        "title"
    ];

    protected $casts = [
        "tested_at" => "datetime"
    ];
}