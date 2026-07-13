<?php

namespace App\Models\ITSas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'data'
    ];

    protected $casts = [
        'date' => 'date',
        'data' => 'array'
    ];
}
