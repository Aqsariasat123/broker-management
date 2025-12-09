<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('created_at', 'desc')->paginate(10);
        
        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('vehicles');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('vehicles');
        
        return view('vehicles.index', compact('vehicles', 'selectedColumns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'regn_no' => 'required|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'useage' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:10',
            'value' => 'nullable|numeric',
            'policy_id' => 'nullable|string|max:255',
            'engine' => 'nullable|string|max:255',
            'engine_type' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:255',
            'engine_no' => 'nullable|string|max:255',
            'chassis_no' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Generate unique VehicleID
        $latest = Vehicle::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('VH', '', $latest->vehicle_id) + 1 : 1001;
        $validated['vehicle_id'] = 'VH' . $nextId;

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function show(Request $request, Vehicle $vehicle)
    {
        if ($request->expectsJson()) {
            return response()->json($vehicle);
        }
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        if (request()->expectsJson()) {
            return response()->json($vehicle);
        }
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'regn_no' => 'required|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'useage' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:10',
            'value' => 'nullable|numeric',
            'policy_id' => 'nullable|string|max:255',
            'engine' => 'nullable|string|max:255',
            'engine_type' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:255',
            'engine_no' => 'nullable|string|max:255',
            'chassis_no' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }

    public function export(Request $request)
    {
        $vehicles = Vehicle::all();

        $fileName = 'vehicles_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $columns = [
            'Regn No', 'Make', 'Model', 'Type', 'Useage', 'Year', 'Value', 'Policy ID', 'Engine',
            'Engine Type', 'CC', 'Engine No', 'Chassis No', 'From', 'To', 'Notes', 'VehicleID'
        ];

        $callback = function() use ($vehicles, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($vehicles as $vh) {
                fputcsv($handle, [
                    $vh->regn_no,
                    $vh->make,
                    $vh->model,
                    $vh->type,
                    $vh->useage,
                    $vh->year,
                    $vh->value,
                    $vh->policy_id,
                    $vh->engine,
                    $vh->engine_type,
                    $vh->cc,
                    $vh->engine_no,
                    $vh->chassis_no,
                    $vh->from,
                    $vh->to,
                    $vh->notes,
                    $vh->vehicle_id,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['vehicle_columns' => $request->columns ?? []]);
        return redirect()->route('vehicles.index')
            ->with('success', 'Column settings saved successfully.');
    }
}
