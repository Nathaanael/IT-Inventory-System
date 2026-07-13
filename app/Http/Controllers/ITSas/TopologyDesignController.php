<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TopologyDesignController extends Controller
{
    /**
     * Display the topology design canvas.
     */
    public function index()
    {
        $path = storage_path('app/topology.json');
        
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
        } else {
            $data = [
                'name' => 'Desain Topologi Baru',
                'date' => date('Y-m-d'),
                'servers' => [
                    [ 'id' => 'server-1', 'name' => 'Server Pusat', 'ip' => '192.168.1.1', 'status' => 'online', 'x' => 400, 'y' => 50 ]
                ],
                'panels' => [
                    [ 
                        'id' => 'panel-1', 'name' => 'Panel Lantai 1', 'x' => 150, 'y' => 300, 'dragOver' => false,
                        'switches' => [
                            [ 'id' => 'sw-1', 'name' => 'Switch Utama', 'ip' => '192.168.1.10', 'merk' => 'Cisco', 'status' => 'online' ],
                            [ 'id' => 'sw-2', 'name' => 'Switch Cadangan', 'ip' => '192.168.1.11', 'merk' => 'Mikrotik', 'status' => 'offline' ],
                        ]
                    ],
                    [ 
                        'id' => 'panel-2', 'name' => 'Panel Lantai 2', 'x' => 550, 'y' => 300, 'dragOver' => false,
                        'switches' => [
                            [ 'id' => 'sw-3', 'name' => 'Switch L2', 'ip' => '192.168.2.10', 'merk' => 'TP-Link', 'status' => 'online' ],
                        ]
                    ]
                ],
                'connections' => [],
                'idCounter' => 10
            ];
        }

        return view('topology.topology', [
            'title' => 'Topology Design',
            'topologyData' => $data
        ]);
    }

    public function save(Request $request)
    {
        $path = storage_path('app/topology.json');
        file_put_contents($path, json_encode($request->all(), JSON_PRETTY_PRINT));
        return response()->json(['success' => true]);
    }
}
