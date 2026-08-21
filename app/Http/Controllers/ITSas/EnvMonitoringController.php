<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnvSensorData;

class EnvMonitoringController extends Controller
{
    public function index()
    {
        $devices = \App\Models\Device::all();
        return view('envmonitoring.envMonitoring', [
            'title' => 'Environment Monitoring',
            'devices' => $devices
        ]);
    }

    public function getData(Request $request)
    {
        $macAddress = $request->query('mac_address');
        
        $query = EnvSensorData::query();
        if ($macAddress) {
            $query->where('mac_address', $macAddress);
        }

        // Data tabel (mengambil 20 data terbaru)
        $sensorData = (clone $query)->orderBy('created_at', 'desc')->take(20)->get();
        
        $tableData = $sensorData->map(function ($item) {
            return [
                'id' => $item->id,
                'waktu' => $item->created_at->format('d M Y, H:i'),
                'suhu' => $item->suhu,
                'kelembaban' => $item->kelembaban,
                'status' => $item->status,
                'is_danger' => ($item->status === 'BAHAYA' || $item->suhu >= 30)
            ];
        });

        // Data grafik (mengurutkan dari yang terlama ke terbaru untuk flow waktu yang benar, ambil 20 terakhir)
        $chartRaw = (clone $query)->orderBy('created_at', 'desc')->take(20)->get()->reverse()->values();
        
        $chartData = $chartRaw->map(function ($item) {
            return [
                'time' => $item->created_at->format('H:i'),
                'suhu' => $item->suhu,
                'kelembaban' => $item->kelembaban
            ];
        });

        return response()->json([
            'table' => $tableData,
            'chart' => $chartData
        ]);
    }
}
