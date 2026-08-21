<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EnvSensorData;

class EnvSensorDataController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mac_address' => 'required|string',
            'ip_address' => 'required|string',
            'suhu' => 'required|numeric',
            'kelembaban' => 'required|numeric',
        ]);

        $device = \App\Models\Device::where('mac_address', $request->mac_address)->first();
        
        $thresholdSuhu = 30; // default
        
        if ($device) {
            $device->update([
                'ip_address' => $request->ip_address,
                'last_seen' => now(),
            ]);
            $thresholdSuhu = $device->threshold_suhu;
        }

        $status = $request->suhu >= $thresholdSuhu ? 'BAHAYA' : 'AMAN';

        $sensorData = EnvSensorData::create([
            'mac_address' => $request->mac_address,
            'suhu' => $request->suhu,
            'kelembaban' => $request->kelembaban,
            'status' => $status
        ]);

        return response()->json([
            'message' => 'Data berhasil diterima',
            'data' => $sensorData
        ], 200);
    }
}
