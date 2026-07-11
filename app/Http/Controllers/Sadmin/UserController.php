<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 5);
        if (!in_array($perPage, [5, 10, 20, 50, 100])) {
            $perPage = 5;
        }

        // Menampilkan semua pengguna termasuk yang sedang login
        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id_karyawan', 'like', "%{$search}%")
                  ->orWhere('username_ad', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $sort = $request->query('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $users = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('master.partials.user_table', compact('users'))->render();
        }

        return view('master.user', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|numeric|unique:users,id_karyawan',
            'name' => 'required|string|max:255',
            'username_ad' => 'required|string|unique:users,username_ad',
            'role' => 'required|in:Super Admin,IT Support'
        ], [
            'id_karyawan.unique' => 'ID Karyawan sudah terdaftar.',
            'id_karyawan.numeric' => 'ID Karyawan hanya boleh berisi angka.',
            'username_ad.unique' => 'Username AD sudah terdaftar.'
        ]);

        $validated['password'] = null; // Default null to force username_ad matching on first login

        $user = User::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'description' => "menambahkan akun staf IT baru: {$user->name} ({$user->role})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|numeric|unique:users,id_karyawan,' . $user->id,
            'name' => 'required|string|max:255',
            'username_ad' => 'required|string|unique:users,username_ad,' . $user->id,
            'role' => 'required|in:Super Admin,IT Support'
        ], [
            'id_karyawan.unique' => 'ID Karyawan sudah terdaftar.',
            'id_karyawan.numeric' => 'ID Karyawan hanya boleh berisi angka.',
            'username_ad.unique' => 'Username AD sudah terdaftar.'
        ]);

        $user->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => "mengubah data akun staf IT: {$user->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cegah penghapusan diri sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('master.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'description' => "menghapus akun staf IT: {$userName}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        $user->password = null;
        $user->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => "mereset Password Login untuk akun: {$user->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.users.index')->with('success', 'Password Login berhasil direset. User dapat membuat password baru saat login kembali.');
    }

    public function resetPin(User $user)
    {
        $user->vault_pin = null;
        $user->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => "mereset Vault PIN untuk akun: {$user->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.users.index')->with('success', 'Vault PIN berhasil direset. User dapat mengatur PIN baru dari menu Vault.');
    }
}
