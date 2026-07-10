<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Panel;
use App\Models\DataSwitch;

class SwitchMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Panel::with(['dataSwitches' => function ($q) use ($search) {
            if ($search) {
                $q->where('merk', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            }
        }]);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('dataSwitches', function ($q) use ($search) {
                      $q->where('merk', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%");
                  });
        }

        $panels = $query->get();
        
        // Total stats (seluruh data, tidak terpengaruh search/pagination)
        $totalSwitches = DataSwitch::count();

        return view('switchmonitoring.switchmonitoring', [
            'title' => 'Switch Monitor',
            'panels' => $panels,
            'totalSwitchesAll' => $totalSwitches,
        ]);
    }

    public function ping(DataSwitch $dataSwitch)
    {
        $ip = $dataSwitch->ip_address;
        
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $command = $isWindows 
            ? "ping -n 1 -w 1000 " . escapeshellarg($ip) 
            : "ping -c 1 -W 1 " . escapeshellarg($ip);
            
        $output = [];
        $result = -1;
        exec($command, $output, $result);
        
        $outputStr = strtolower(implode(" ", $output));
        $isOnline = strpos($outputStr, 'ttl=') !== false;
        
        return response()->json([
            'status' => $isOnline ? 'online' : 'offline',
            'ip' => $ip
        ]);
    }
}

