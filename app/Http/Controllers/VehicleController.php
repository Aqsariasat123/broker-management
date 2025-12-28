<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Policy;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $policyId = $request->get('policy_id');
        $clientId = $request->get('client_id');
        $policy = null;
        
        if ($policyId) {
            $policy = Policy::with('client')->findOrFail($policyId);
            $vehicles = Vehicle::where('policy_id', $policyId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($clientId) {
            // Filter vehicles by client_id through policy relationship
            $vehicles = Vehicle::whereHas('policy', function($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })
            ->with('policy.client')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        } else {
            // Show all vehicles without a policy_id or all vehicles
            $vehicles = Vehicle::whereNull('policy_id')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        
        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('vehicles');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('vehicles');
        
        // Get client information if filtering by client_id
        $client = null;
        if ($clientId) {
            $client = \App\Models\Client::find($clientId);
        }
        
        return view('vehicles.index', compact('vehicles', 'selectedColumns', 'policy', 'policyId', 'client', 'clientId'));
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
        $nextId = $latest ? (int)str_replace('VH', '', $latest->vehicle_id ?? 'VH0') + 1 : 1001;
        $validated['vehicle_id'] = 'VH' . $nextId;

        $vehicle = Vehicle::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle created successfully.',
                'vehicle' => $vehicle
            ]);
        }

        // if (isset($validated['policy_id']) && $validated['policy_id']) {
        //     return redirect()->route('vehicles.index', ['policy_id' => $validated['policy_id']])
        //         ->with('success', 'Vehicle created successfully.');
        // }

        // Redirect to vehicles index without policy_id to show unlinked vehicles
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle created successfully. Please save the policy to link this vehicle.');
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

        $redirectParams = $vehicle->policy_id 
            ? ['policy_id' => $vehicle->policy_id] 
            : [];
        
        return redirect()->route('vehicles.index', $redirectParams)
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $policyId = $vehicle->policy_id;
        $vehicle->delete();

        $redirectParams = $policyId ? ['policy_id' => $policyId] : [];
        
        return redirect()->route('vehicles.index', $redirectParams)
            ->with('success', 'Vehicle deleted successfully.');
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
