<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Panel;
use App\Models\DataSwitch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;

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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('dataSwitches', function ($sub) use ($search) {
                      $sub->where('merk', 'like', "%{$search}%")
                          ->orWhere('ip_address', 'like', "%{$search}%");
                  });
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

    /**
     * Get status of ALL switches. Uses Concurrent Process Pooling.
     * Caches the result for 25 seconds. If cache misses, acquires a lock 
     * and pings all switches concurrently (takes ~1 second).
     */
    public function status()
    {
        // Try to get from cache first
        $cached = Cache::get('switch_statuses_all');
        if ($cached) {
            return response()->json($cached);
        }

        // Cache expired. Use atomic lock to prevent stampede (max wait 3 seconds)
        // If multiple users request at the exact same time, only 1 will do the pinging.
        $statuses = Cache::lock('ping_switches_lock', 5)->block(3, function () {
            // Check again inside the lock in case another process just finished pinging
            $cachedInsideLock = Cache::get('switch_statuses_all');
            if ($cachedInsideLock) {
                return $cachedInsideLock;
            }

            $switches = DataSwitch::select('id', 'ip_address')->get();
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            // Build concurrent process pool
            $poolResults = Process::pool(function (Pool $pool) use ($switches, $isWindows) {
                foreach ($switches as $switch) {
                    $ip = trim($switch->ip_address ?? '');
                    if (empty($ip)) {
                        continue;
                    }
                    
                    $command = $isWindows
                        ? "C:\\Windows\\System32\\ping.exe -n 1 -w 1000 " . escapeshellarg($ip)
                        : "ping -c 1 -W 1 " . escapeshellarg($ip);
                        
                    $pool->as("switch_{$switch->id}")->command($command);
                }
            })->start()->wait();

            $newStatuses = [];
            foreach ($switches as $switch) {
                $id = "switch_{$switch->id}";
                $newStatuses[$switch->id] = 'offline'; // Default

                if (isset($poolResults[$id])) {
                    $output = strtolower($poolResults[$id]->output());
                    if (strpos($output, 'ttl=') !== false) {
                        $newStatuses[$switch->id] = 'online';
                    }
                } else {
                    \Log::warning("Pool result not found for id: {$id}");
                }
            }

            // Save to cache for 25 seconds (since polling is every 30s)
            Cache::put('switch_statuses_all', $newStatuses, 25);
            \Log::info('Ping finished. Results: ', $newStatuses);

            return $newStatuses;
        });

        // In case block() fails (timeout), we return empty/offline to be safe, 
        // though $statuses will usually be populated.
        if ($statuses === false) {
            return response()->json([]);
        }

        return response()->json($statuses);
    }

    /**
     * Manual ping for a single switch (debug/manual refresh).
     * Rate limited via route middleware (throttle:10,1).
     */
    public function ping($id)
    {
        $dataSwitch = DataSwitch::find($id);

        if (!$dataSwitch) {
            return response()->json(['status' => 'offline', 'error' => 'not_found'], 404);
        }

        $ip = trim($dataSwitch->ip_address ?? '');

        // Validate IP is not empty before exec
        if (empty($ip)) {
            return response()->json(['status' => 'offline', 'error' => 'no_ip_address'], 422);
        }

        try {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            $command = $isWindows
                ? "C:\\Windows\\System32\\ping.exe -n 1 -w 1000 " . escapeshellarg($ip)
                : "ping -c 1 -W 1 " . escapeshellarg($ip);

            $output = [];
            $result = -1;
            exec($command, $output, $result);

            $outputStr = strtolower(implode(" ", $output));
            $isOnline = strpos($outputStr, 'ttl=') !== false;

            $status = $isOnline ? 'online' : 'offline';

            // Update the bulk cache array specifically for this switch
            $cachedAll = Cache::get('switch_statuses_all', []);
            $cachedAll[$id] = $status;
            Cache::put('switch_statuses_all', $cachedAll, 25);

            return response()->json(['status' => $status, 'ip' => $ip]);
        } catch (\Throwable $e) {
            \Log::warning("Ping error switch {$id} ({$ip}): " . $e->getMessage());
            return response()->json(['status' => 'offline', 'ip' => $ip, 'error' => 'exception'], 500);
        }
    }
}
