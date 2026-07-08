<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);
        if (!in_array($perPage, [5, 10, 20, 50, 100])) {
            $perPage = 5;
        }

        $query = ActivityLog::select('activity_logs.*')
                    ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
                    ->with('user');

        $sort = $request->query('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('activity_logs.created_at', 'asc');
        } elseif ($sort === 'user_asc') {
            $query->orderBy('users.name', 'asc');
        } elseif ($sort === 'user_desc') {
            $query->orderBy('users.name', 'desc');
        } elseif ($sort === 'action_asc') {
            $query->orderBy('activity_logs.action', 'asc');
        } elseif ($sort === 'action_desc') {
            $query->orderBy('activity_logs.action', 'desc');
        } else {
            $query->orderBy('activity_logs.created_at', 'desc');
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('activity_logs.description', 'like', "%{$search}%")
                  ->orWhere('activity_logs.action', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%")
                  ->orWhere('users.id_karyawan', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('audit.partials.table', compact('logs'))->render();
        }

        return view('audit.audit', compact('logs'));
    }
}
