<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeviceManagerController extends Controller
{
    public function index()
    {
        $devices = \App\Models\Device::all();
        return view('devicemanager.devicemanager', [
            'title' => 'Device Manager',
            'devices' => $devices
        ]);
    }

    public function create()
    {
        return view('devicemanager.create', ['title' => 'Tambah Perangkat IoT']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perangkat' => 'required|string|max:255',
            'mac_address' => 'required|string|unique:devices,mac_address',
            'lokasi' => 'required|string|max:255',
            'threshold_suhu' => 'required|numeric',
        ]);

        \App\Models\Device::create([
            'nama_perangkat' => $request->nama_perangkat,
            'mac_address' => $request->mac_address,
            'lokasi' => $request->lokasi,
            'threshold_suhu' => $request->threshold_suhu,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('devicemanager.index')->with('success', 'Perangkat berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $device = \App\Models\Device::findOrFail($id);
        
        try {
            $device->password_wifi = \Illuminate\Support\Facades\Crypt::decryptString($device->password_wifi);
        } catch (\Exception $e) {
            $device->password_wifi = ''; // fallback if not encrypted properly
        }

        return view('devicemanager.edit', [
            'title' => 'Edit Perangkat IoT',
            'device' => $device
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_perangkat' => 'required|string|max:255',
            'mac_address' => 'required|string|unique:devices,mac_address,'.$id,
            'lokasi' => 'required|string|max:255',
            'threshold_suhu' => 'required|numeric',
        ]);

        $device = \App\Models\Device::findOrFail($id);
        
        $device->update([
            'nama_perangkat' => $request->nama_perangkat,
            'mac_address' => $request->mac_address,
            'lokasi' => $request->lokasi,
            'threshold_suhu' => $request->threshold_suhu,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('devicemanager.index')->with('success', 'Perangkat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $device = \App\Models\Device::findOrFail($id);
        $device->delete();

        return redirect()->route('devicemanager.index')->with('success', 'Perangkat berhasil dihapus!');
    }

    public function ping($id)
    {
        $device = \App\Models\Device::findOrFail($id);
        $ip = $device->ip_address;
        
        if (empty($ip)) {
            return response()->json([
                'status' => 'offline',
                'ip' => '-'
            ]);
        }

        // Windows Ping: ping -n 1 -w 1000 IP
        // Linux Ping: ping -c 1 -W 1 IP
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $command = $isWindows 
            ? "ping -n 1 -w 1000 " . escapeshellarg($ip) 
            : "ping -c 1 -W 1 " . escapeshellarg($ip);
            
        $output = [];
        $result = -1;
        exec($command, $output, $result);
        
        $outputStr = strtolower(implode(" ", $output));
        $isOnline = false;
        
        if (strpos($outputStr, 'ttl=') !== false) {
            $isOnline = true;
        }
        
        return response()->json([
            'status' => $isOnline ? 'online' : 'offline',
            'ip' => $ip
        ]);
    }
}
