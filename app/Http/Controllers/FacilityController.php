<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Organization;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities (optionally filtered by organization).
     */
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        $facilities = $query->with('organization')->get();

        return response()->json($facilities);
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:corporate,commercial,residential,school,hospital,industrial',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $facility = Facility::create($validated);

        return response()->json([
            'message' => 'Facility created successfully!',
            'facility' => $facility->load('organization')
        ], 201);
    }

    /**
     * Display the specified facility.
     */
    public function show(Facility $facility)
    {
        return response()->json($facility->load(['organization', 'subUnits', 'people']));
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:corporate,commercial,residential,school,hospital,industrial',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'settings' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $facility->update($validated);

        return response()->json([
            'message' => 'Facility updated successfully!',
            'facility' => $facility
        ]);
    }

    /**
     * Remove the specified facility from storage.
     */
    public function destroy(Facility $facility)
    {
        $facility->delete();

        return response()->json([
            'message' => 'Facility deleted successfully!'
        ]);
    }
}