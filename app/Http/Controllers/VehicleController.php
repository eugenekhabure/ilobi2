<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Person;
use App\Models\Visitor;
use App\Models\Facility;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['facility', 'owner']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        if ($request->has('owner_type') && $request->has('owner_id')) {
            $query->where('owner_type', $request->owner_type)
                  ->where('owner_id', $request->owner_id);
        }
        if ($request->has('plate_number')) {
            $query->where('plate_number', 'LIKE', '%' . $request->plate_number . '%');
        }

        $vehicles = $query->get();

        if ($request->wantsJson()) {
            return response()->json($vehicles);
        }

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $facilities = Facility::all();
        $people = Person::all();
        return view('admin.vehicles.create', compact('facilities', 'people'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'plate_number' => 'required|string|max:20',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'owner_type' => 'required|string|in:App\Models\Person,App\Models\Visitor',
            'owner_id' => 'required|integer',
        ]);

        $ownerClass = $validated['owner_type'];
        $owner = $ownerClass::find($validated['owner_id']);
        if (!$owner) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'The specified owner does not exist.'], 404);
            }
            return redirect()->back()->with('error', 'The specified owner does not exist.');
        }

        $existing = Vehicle::where('facility_id', $validated['facility_id'])
            ->where('plate_number', $validated['plate_number'])
            ->first();

        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'A vehicle with this plate number already exists.'], 422);
            }
            return redirect()->back()->with('error', 'A vehicle with this plate number already exists.');
        }

        $vehicle = Vehicle::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Vehicle registered successfully!',
                'vehicle' => $vehicle->load(['facility', 'owner'])
            ], 201);
        }

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle registered successfully!');
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json($vehicle->load(['facility', 'owner']));
    }

    public function edit(Vehicle $vehicle)
    {
        $facilities = Facility::all();
        $people = Person::all();
        return view('admin.vehicles.edit', compact('vehicle', 'facilities', 'people'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'sometimes|string|max:20',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'facility_id' => 'sometimes|exists:facilities,id',
        ]);

        $vehicle->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Vehicle updated successfully!',
                'vehicle' => $vehicle
            ]);
        }

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Vehicle deleted successfully!']);
        }

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully!');
    }

    public function getByOwner($ownerType, $ownerId)
    {
        $vehicles = Vehicle::where('owner_type', 'App\Models\\' . ucfirst($ownerType))
            ->where('owner_id', $ownerId)
            ->with('facility')
            ->get();

        return response()->json($vehicles);
    }

    public function search($facilityId, $query)
    {
        $vehicles = Vehicle::where('facility_id', $facilityId)
            ->where('plate_number', 'LIKE', '%' . $query . '%')
            ->with(['facility', 'owner'])
            ->get();

        return response()->json($vehicles);
    }
}