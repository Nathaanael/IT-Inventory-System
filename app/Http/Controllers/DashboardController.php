<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\EnvSensorData;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'all'); // Default or requested period
        
        $dateFilter = function($query) use ($period) {
            if ($period === 'today') {
                $query->whereDate('created_at', \Carbon\Carbon::today());
            } elseif ($period === '7days') {
                $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7));
            } elseif ($period === '30days') {
                $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30));
            } elseif ($period === 'lastmonth') {
                $query->whereMonth('created_at', \Carbon\Carbon::now()->subMonth()->month)
                      ->whereYear('created_at', \Carbon\Carbon::now()->subMonth()->year);
            }
        };

        // 1. Total PC/User (Inventory Count)
        $inventoryQuery = Inventory::query();
        $dateFilter($inventoryQuery);
        $totalInventory = $inventoryQuery->count();
        
        // Departments usually don't need period filter, but we'll leave it as total
        $totalDepartments = Department::count();

        // 2. Chart Data: Inventory Count per Department
        $chartQuery = Inventory::select('department_id', DB::raw('count(*) as total'))
            ->with('department')
            ->groupBy('department_id');
        $dateFilter($chartQuery);
        $departmentStats = $chartQuery->get();
            
        $chartData = [
            'Semua' => ['labels' => [], 'series' => []],
            'HO' => ['labels' => [], 'series' => []],
            'BP' => ['labels' => [], 'series' => []],
            'PR' => ['labels' => [], 'series' => []],
        ];

        foreach ($departmentStats as $stat) {
            $deptName = $stat->department ? $stat->department->name : 'Unknown';
            $unit = $stat->department ? $stat->department->unit : '';

            $chartData['Semua']['labels'][] = $deptName;
            $chartData['Semua']['series'][] = $stat->total;

            if ($unit && isset($chartData[$unit])) {
                $chartData[$unit]['labels'][] = $deptName;
                $chartData[$unit]['series'][] = $stat->total;
            }
        }

        // 3. Recent Activities
        $logPerPage = (int) $request->get('log_per_page', 5);
        if (!in_array($logPerPage, [5, 10, 20, 50])) {
            $logPerPage = 5;
        }
        $logQuery = ActivityLog::with('user')->latest();
        $dateFilter($logQuery);
        $recentActivities = $logQuery->paginate($logPerPage)->withQueryString();

        // 4. Server Room Averages (C8:85:41:C5:E2:44)
        $serverRoomQuery = EnvSensorData::where('mac_address', 'C8:85:41:C5:E2:44');
        $dateFilter($serverRoomQuery);
        $avgSuhu = $serverRoomQuery->avg('suhu') ?? 0;
        $avgKelembaban = $serverRoomQuery->avg('kelembaban') ?? 0;

        return view('dashboard.itsas', compact(
            'totalInventory', 
            'totalDepartments',
            'chartData', 
            'recentActivities',
            'avgSuhu',
            'avgKelembaban'
        ));
    }
}
