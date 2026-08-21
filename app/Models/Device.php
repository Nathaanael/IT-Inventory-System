<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perangkat',
        'mac_address',
        'lokasi',
        'threshold_suhu',
        'keterangan',
        'ip_address',
        'status',
        'last_seen',
    ];
}
