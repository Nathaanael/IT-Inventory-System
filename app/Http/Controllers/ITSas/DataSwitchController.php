<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Panel;
use App\Models\DataSwitch;

class DataSwitchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Panel::with(['dataSwitches' => function ($q) use ($search) {
            if ($search) {
                $q->where('merk', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            }
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('dataSwitches', function ($sub) use ($search) {
                      $sub->where('merk', 'like', "%{$search}%")
                          ->orWhere('ip_address', 'like', "%{$search}%")
                          ->orWhere('notes', 'like', "%{$search}%");
                  });
            });
        }

        $panels = $query->paginate(5)->withQueryString();
        $totalPanels = Panel::count();
        $totalSwitches = DataSwitch::count();
        
        return view('dataswitch.dataswitch', [
            'title' => 'Data Switch',
            'panels' => $panels,
            'totalPanels' => $totalPanels,
            'totalSwitches' => $totalSwitches
        ]);
    }

    public function storePanel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        Panel::create($validated);

        return redirect()->back()->with('success', 'Panel berhasil ditambahkan.');
    }

    public function storeSwitch(Request $request)
    {
        $validated = $request->validate([
            'panel_id' => 'required|exists:panels,id',
            'merk' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:data_switches,ip_address|max:255',
            'notes' => 'nullable|string',
        ]);

        DataSwitch::create($validated);

        return redirect()->back()->with('success', 'Switch berhasil ditambahkan.');
    }

    public function destroySwitch(DataSwitch $dataSwitch)
    {
        $dataSwitch->delete();
        return redirect()->back()->with('success', 'Data Switch berhasil dihapus.');
    }

    public function updateSwitch(Request $request, DataSwitch $dataSwitch)
    {
        $validated = $request->validate([
            'panel_id' => 'required|exists:panels,id',
            'merk' => 'required|string|max:255',
            'ip_address' => 'required|ip|max:255|unique:data_switches,ip_address,' . $dataSwitch->id,
            'notes' => 'nullable|string',
        ]);

        $dataSwitch->update($validated);

        return redirect()->back()->with('success', 'Data Switch berhasil diperbarui.');
    }

    public function updatePanel(Request $request, Panel $panel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $panel->update($validated);

        return redirect()->back()->with('success', 'Panel berhasil diperbarui.');
    }

    public function destroyPanel(Panel $panel)
    {
        // Delete all associated switches first, or cascade will handle it if configured
        $panel->dataSwitches()->delete();
        $panel->delete();

        return redirect()->back()->with('success', 'Panel beserta Switch di dalamnya berhasil dihapus.');
    }
}
