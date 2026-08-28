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
        $query = $this->buildQuery($request);

        // Data tabel (mengambil 20 data terbaru)
        $sensorData = (clone $query)->with('device')->orderBy('created_at', 'desc')->take(20)->get();
        
        $tableData = $sensorData->map(function ($item) {
            return [
                'id' => $item->id,
                'waktu' => $item->created_at->format('d M Y, H:i'),
                'perangkat' => $item->device ? $item->device->nama_perangkat : 'Unknown Device',
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

    private function buildQuery(Request $request)
    {
        $macAddress = $request->input('mac_address');
        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = EnvSensorData::query();

        if ($macAddress) {
            $query->where('mac_address', $macAddress);
        }

        if ($period === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($period === 'today') {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($period === '7days') {
            $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30));
        }

        return $query;
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildQuery($request);
        $data = $query->with('device')->orderBy('created_at', 'desc')->get();

        $summary = [
            'maxTemp' => $data->max('suhu') ?? 0,
            'minTemp' => $data->min('suhu') ?? 0,
            'avgTemp' => $data->avg('suhu') ? round($data->avg('suhu'), 2) : 0,
            'dangerCount' => $data->filter(function($item) {
                return $item->status === 'BAHAYA' || $item->suhu >= 30;
            })->count(),
        ];

        $tempImage = $request->input('temp_image');
        $humidImage = $request->input('humid_image');
        $period = $request->input('period', 'today');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('envmonitoring.pdf', compact('data', 'summary', 'tempImage', 'humidImage', 'period'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Monitoring_Lingkungan.pdf');
    }
}
