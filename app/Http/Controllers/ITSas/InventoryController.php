<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Department;
use App\Models\Inventory;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 5);
        $unit = $request->query('unit', 'Semua');
        
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 5;
        }

        $sort = $request->query('sort', 'latest');

        $query = Inventory::with(['department', 'creator']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_user', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('department', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($unit !== 'Semua') {
            $query->whereHas('department', function($q) use ($unit) {
                $q->where('unit', $unit);
            });
        }

        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $inventories = $query->paginate($perPage)->withQueryString();

        $todayNewInventories = Inventory::whereDate('created_at', today())->with('department')->get();
        
        $newCount = [
            'Semua' => $todayNewInventories->count(),
            'HO' => $todayNewInventories->where('department.unit', 'HO')->count(),
            'BP' => $todayNewInventories->where('department.unit', 'BP')->count(),
            'PR' => $todayNewInventories->where('department.unit', 'PR')->count(),
        ];

        return view('inventory.inventory', compact('inventories', 'perPage', 'unit', 'newCount'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('inventory.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'departemen' => 'required|exists:departments,id',
            'ip_address' => 'required|ip|unique:inventories,ip_address',
            'password_remote' => 'nullable|string',
            'id_karyawan' => 'nullable|numeric',
            'username_ad' => 'nullable|string|max:255',
            'nomor_asset_pc' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'ip_address.unique' => 'IP Address ini sudah terdaftar dan digunakan oleh user lain.'
        ]);

        $inventory = Inventory::create([
            'nama_user' => $validated['nama_user'],
            'id_karyawan' => $validated['id_karyawan'] ?? null,
            'username_ad' => $validated['username_ad'] ?? null,
            'nomor_asset_pc' => $validated['nomor_asset_pc'] ?? null,
            'department_id' => $validated['departemen'],
            'ip_address' => $validated['ip_address'],
            'password_remote' => !empty($validated['password_remote']) ? Crypt::encryptString($validated['password_remote']) : null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);
        
        $departmentName = Department::find($validated['departemen'])->name;

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'description' => "menambahkan data IP {$departmentName} untuk {$validated['nama_user']}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('inventory.index')->with('success', 'Data inventory berhasil ditambahkan.');
    }

    public function edit(Inventory $inventory)
    {
        // Check if vault is unlocked in session
        if (!session('vault_unlocked') || session('vault_unlocked_expires_at') < now() || auth()->user()->vault_pin === null) {
            return redirect()->route('inventory.index')->with('error', 'Akses ditolak. Anda harus mengatur/memasukkan Vault PIN terlebih dahulu.');
        }

        $departments = Department::all();
        // Decrypt password for editing
        try {
            $inventory->password_remote = $inventory->password_remote ? Crypt::decryptString($inventory->password_remote) : '';
        } catch (DecryptException $e) {
            $inventory->password_remote = '';
        }
        return view('inventory.edit', compact('inventory', 'departments'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'departemen' => 'required|exists:departments,id',
            'ip_address' => 'required|ip|unique:inventories,ip_address,' . $inventory->id,
            'password_remote' => 'nullable|string',
            'id_karyawan' => 'nullable|numeric',
            'username_ad' => 'nullable|string|max:255',
            'nomor_asset_pc' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'ip_address.unique' => 'IP Address ini sudah terdaftar dan digunakan oleh user lain.'
        ]);

        $inventory->update([
            'nama_user' => $validated['nama_user'],
            'id_karyawan' => $validated['id_karyawan'] ?? null,
            'username_ad' => $validated['username_ad'] ?? null,
            'nomor_asset_pc' => $validated['nomor_asset_pc'] ?? null,
            'department_id' => $validated['departemen'],
            'ip_address' => $validated['ip_address'],
            'password_remote' => !empty($validated['password_remote']) ? Crypt::encryptString($validated['password_remote']) : null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        $departmentName = Department::find($validated['departemen'])->name;

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => "mengubah data IP {$departmentName} untuk {$validated['nama_user']}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('inventory.index')->with('success', 'Data inventory berhasil diperbarui.');
    }

    public function destroy(Inventory $inventory)
    {
        $userName = $inventory->nama_user;
        $inventory->delete();
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'description' => "menghapus data inventory untuk {$userName}",
            'ip_address' => request()->ip()
        ]);
        return redirect()->route('inventory.index')->with('success', 'Data inventory berhasil dihapus.');
    }

    public function setPin(Request $request)
    {
        $user = auth()->user();
        if ($user->vault_pin !== null) {
            return response()->json(['success' => false, 'message' => 'Vault PIN sudah diatur.']);
        }

        $request->validate([
            'pin' => 'required|digits:6|confirmed'
        ], [
            'pin.digits' => 'PIN harus terdiri dari 6 angka.',
            'pin.confirmed' => 'Konfirmasi PIN tidak cocok.'
        ]);

        $user->vault_pin = Hash::make($request->pin);
        $user->save();

        session(['vault_unlocked' => true, 'vault_unlocked_expires_at' => now()->addMinutes(15)]);

        return response()->json(['success' => true]);
    }

    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:6']);
        $user = auth()->user();

        if (Hash::check($request->pin, $user->vault_pin)) {
            session(['vault_unlocked' => true, 'vault_unlocked_expires_at' => now()->addMinutes(15)]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'PIN yang Anda masukkan salah.'], 403);
    }

    public function revealPassword(Request $request, Inventory $inventory)
    {
        $request->validate(['pin' => 'required|digits:6']);
        $user = auth()->user();

        if (Hash::check($request->pin, $user->vault_pin)) {
            if (empty($inventory->password_remote)) {
                return response()->json([
                    'success' => true,
                    'password' => '-'
                ]);
            }
            try {
                $password = Crypt::decryptString($inventory->password_remote);
            } catch (DecryptException $e) {
                return response()->json(['success' => false, 'message' => 'Data password tidak dapat di-dekripsi.'], 500);
            }
            return response()->json([
                'success' => true,
                'password' => $password
            ]);
        }

        return response()->json(['success' => false, 'message' => 'PIN yang Anda masukkan salah.'], 403);
    }

    public function ping(Inventory $inventory)
    {
        $ip = $inventory->ip_address;
        
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

    public function bulkPing(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:inventories,id'
        ]);

        $ids = $request->ids;
        $inventories = Inventory::whereIn('id', $ids)->select('id', 'ip_address')->get();
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $poolResults = Process::pool(function (Pool $pool) use ($inventories, $isWindows) {
            foreach ($inventories as $inv) {
                $ip = trim($inv->ip_address ?? '');
                if (empty($ip)) continue;
                
                $command = $isWindows
                    ? "C:\\Windows\\System32\\ping.exe -n 1 -w 1000 " . escapeshellarg($ip)
                    : "ping -c 1 -W 1 " . escapeshellarg($ip);
                    
                $pool->as("inv_{$inv->id}")->command($command);
            }
        })->start()->wait();

        $statuses = [];
        foreach ($inventories as $inv) {
            $id = "inv_{$inv->id}";
            $statuses[$inv->id] = 'offline';

            if (isset($poolResults[$id])) {
                $output = strtolower($poolResults[$id]->output());
                if (strpos($output, 'ttl=') !== false) {
                    $statuses[$inv->id] = 'online';
                }
            }
        }

        return response()->json($statuses);
    }

    public function downloadRdp(Inventory $inventory)
    {
        $ip = $inventory->ip_address;
        // Clean name for safe filename
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $inventory->nama_user);
        $filename = "Remote_{$cleanName}_{$ip}.rdp";
        
        $content = "full address:s:{$ip}\r\n";
        $content .= "prompt for credentials:i:1\r\n";
        $content .= "screen mode id:i:2\r\n"; // Fullscreen
        
        return response($content)
            ->header('Content-Type', 'application/x-rdp')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
