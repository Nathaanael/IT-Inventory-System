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
}
