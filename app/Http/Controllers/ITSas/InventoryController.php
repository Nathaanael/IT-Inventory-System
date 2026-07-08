<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Department;
use App\Models\Inventory;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 5); // Default 5
        
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

        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $inventories = $query->paginate($perPage)->withQueryString();

        return view('inventory.inventory', compact('inventories', 'perPage'));
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
            'password_remote' => 'required|string',
        ], [
            'ip_address.unique' => 'IP Address ini sudah terdaftar dan digunakan oleh user lain.'
        ]);

        $inventory = Inventory::create([
            'nama_user' => $validated['nama_user'],
            'department_id' => $validated['departemen'],
            'ip_address' => $validated['ip_address'],
            'password_remote' => Crypt::encryptString($validated['password_remote']),
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
        if (!session('vault_unlocked') || session('vault_unlocked_expires_at') < now()) {
            return redirect()->route('inventory.index')->with('error', 'Akses ditolak. Anda harus memasukkan Vault PIN terlebih dahulu.');
        }

        $departments = Department::all();
        // Decrypt password for editing (only if needed or just pass an indicator)
        $inventory->password_remote = Crypt::decryptString($inventory->password_remote);
        return view('inventory.edit', compact('inventory', 'departments'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'departemen' => 'required|exists:departments,id',
            'ip_address' => 'required|ip|unique:inventories,ip_address,' . $inventory->id,
            'password_remote' => 'required|string',
        ], [
            'ip_address.unique' => 'IP Address ini sudah terdaftar dan digunakan oleh user lain.'
        ]);

        $inventory->update([
            'nama_user' => $validated['nama_user'],
            'department_id' => $validated['departemen'],
            'ip_address' => $validated['ip_address'],
            'password_remote' => Crypt::encryptString($validated['password_remote']),
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
        $request->validate([
            'pin' => 'required|digits:6|confirmed'
        ], [
            'pin.digits' => 'PIN harus terdiri dari 6 angka.',
            'pin.confirmed' => 'Konfirmasi PIN tidak cocok.'
        ]);

        $user = auth()->user();
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
            return response()->json([
                'success' => true,
                'password' => Crypt::decryptString($inventory->password_remote)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'PIN yang Anda masukkan salah.'], 403);
    }
}
