<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvSensorData extends Model
{
    protected $table = 'env_sensor_data';
    protected $fillable = [
        'mac_address',
        'suhu',
        'kelembaban',
        'status',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'mac_address', 'mac_address');
    }
}
