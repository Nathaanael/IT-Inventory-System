<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);
        if (!in_array($perPage, [5, 10, 20, 50, 100])) {
            $perPage = 5;
        }

        $query = Department::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sort = $request->query('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->latest();
        }

        $departments = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('master.partials.department_table', compact('departments'))->render();
        }

        return view('master.department', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name'
        ], [
            'name.unique' => 'Nama departemen sudah terdaftar.'
        ]);

        $department = Department::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'description' => "menambahkan master departemen baru: {$department->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id
        ], [
            'name.unique' => 'Nama departemen sudah terdaftar.'
        ]);

        $oldName = $department->name;
        $department->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'description' => "mengubah nama master departemen dari {$oldName} menjadi {$department->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        // Check if there are inventories using this department
        if ($department->inventories()->count() > 0) {
            return redirect()->route('master.departments.index')->with('error', 'Tidak dapat menghapus departemen ini karena sedang digunakan oleh data PC/Remote (Inventory).');
        }

        $deptName = $department->name;
        $department->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'description' => "menghapus master departemen: {$deptName}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('master.departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
