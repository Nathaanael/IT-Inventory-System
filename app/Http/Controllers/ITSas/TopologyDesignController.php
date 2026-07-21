<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ITSas\Topology;

class TopologyDesignController extends Controller
{
    /**
     * Display the topology design canvas.
     */
    public function index()
    {
        $topology = Topology::first();
        
        if ($topology) {
            $data = $topology->data;
            // Sinkronkan nama dan tanggal dengan field dari tabel
            $data['name'] = $topology->name;
            $data['date'] = $topology->date ? $topology->date->format('Y-m-d') : date('Y-m-d');
        } else {
            $data = [
                'name' => 'Desain Topologi Baru',
                'date' => date('Y-m-d'),
                'servers' => [],
                'panels' => [],
                'gedungs' => [],
                'internets' => [],
                'routers' => [],
                'connections' => [],
                'idCounter' => 1
            ];
        }

        return view('topology.topology', [
            'title' => 'Topology Design',
            'topologyData' => $data
        ]);
    }

    public function save(Request $request)
    {
        $topology = Topology::first();
        if (!$topology) {
            $topology = new Topology();
        }
        
        $topology->name = $request->input('name', 'Desain Topologi Baru');
        $topology->date = $request->input('date', date('Y-m-d'));
        $topology->data = $request->all();
        $topology->save();

        return response()->json(['success' => true]);
    }

    /**
     * Perform live concurrent ping on requested IPs.
     */
    public function livePing(Request $request)
    {
        $ips = $request->input('ips', []);
        if (!is_array($ips)) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Clean and validate IPs
        $validIps = [];
        foreach ($ips as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $validIps[] = $ip;
            }
        }
        $validIps = array_unique($validIps);

        if (empty($validIps)) {
            return response()->json([]);
        }

        try {
            $statuses = \Illuminate\Support\Facades\Cache::lock('topology_ping_lock', 5)->block(3, function () use ($validIps) {
                $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
                
                // Build concurrent process pool
                $poolResults = \Illuminate\Support\Facades\Process::pool(function (\Illuminate\Process\Pool $pool) use ($validIps, $isWindows) {
                    foreach ($validIps as $index => $ip) {
                        $command = $isWindows
                            ? "C:\\Windows\\System32\\ping.exe -n 1 -w 1000 " . escapeshellarg($ip)
                            : "ping -c 1 -W 1 " . escapeshellarg($ip);
                            
                        $pool->as("ping_{$index}")->command($command);
                    }
                })->start()->wait();

                $results = [];
                foreach ($validIps as $index => $ip) {
                    $id = "ping_{$index}";
                    $results[$ip] = 'offline'; // Default

                    if (isset($poolResults[$id])) {
                        $output = strtolower($poolResults[$id]->output());
                        if (strpos($output, 'ttl=') !== false) {
                            $results[$ip] = 'online';
                        }
                    }
                }
                return $results;
            });
            
            return response()->json($statuses ?? []);
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            // Lock timeout, return empty or a message indicating busy
            return response()->json(['error' => 'Server is busy pinging right now. Please try again in a few seconds.'], 429);
        }
    }
}
