<?php

namespace App\Http\Controllers;

use App\Models\SubUnit;
use App\Models\Facility;
use Illuminate\Http\Request;

class SubUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = SubUnit::query();

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $subUnits = $query->with(['facility', 'parent', 'children'])->get();

        if ($request->wantsJson()) {
            return response()->json($subUnits);
        }

        return view('admin.sub-units.index', compact('subUnits'));
    }

    public function create()
    {
        $facilities = Facility::all();
        $parentUnits = SubUnit::all();
        return view('admin.sub-units.create', compact('facilities', 'parentUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'parent_id' => 'nullable|exists:sub_units,id',
            'type' => 'required|in:floor,block,wing,tower,street,unit,apartment',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subUnit = SubUnit::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sub-unit created successfully!',
                'sub_unit' => $subUnit->load(['facility', 'parent', 'children'])
            ], 201);
        }

        return redirect()->route('admin.sub-units.index')
            ->with('success', 'Sub-unit created successfully!');
    }

    public function show(SubUnit $subUnit)
    {
        return response()->json(
            $subUnit->load(['facility', 'parent', 'children', 'residentProfiles.person'])
        );
    }

    public function edit(SubUnit $subUnit)
    {
        $facilities = Facility::all();
        $parentUnits = SubUnit::where('id', '!=', $subUnit->id)->get();
        return view('admin.sub-units.edit', compact('subUnit', 'facilities', 'parentUnits'));
    }

    public function update(Request $request, SubUnit $subUnit)
    {
        $validated = $request->validate([
            'facility_id' => 'sometimes|exists:facilities,id',
            'parent_id' => 'nullable|exists:sub_units,id',
            'type' => 'sometimes|in:floor,block,wing,tower,street,unit,apartment',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subUnit->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sub-unit updated successfully!',
                'sub_unit' => $subUnit
            ]);
        }

        return redirect()->route('admin.sub-units.index')
            ->with('success', 'Sub-unit updated successfully!');
    }

    public function destroy(SubUnit $subUnit)
    {
        if ($subUnit->children()->count() > 0) {
            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'Cannot delete this unit because it has child units.'
                ], 422);
            }
            return redirect()->route('admin.sub-units.index')
                ->with('error', 'Cannot delete this unit because it has child units.');
        }

        $subUnit->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Sub-unit deleted successfully!'
            ]);
        }

        return redirect()->route('admin.sub-units.index')
            ->with('success', 'Sub-unit deleted successfully!');
    }

    public function getTree($facilityId)
    {
        $topLevelUnits = SubUnit::where('facility_id', $facilityId)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        return response()->json($topLevelUnits);
    }
}