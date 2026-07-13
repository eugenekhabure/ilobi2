<?php

namespace App\Http\Controllers;

use App\Models\ResidentProfile;
use App\Models\Person;
use App\Models\SubUnit;
use App\Models\Facility;
use Illuminate\Http\Request;

class ResidentProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = ResidentProfile::with(['person', 'subUnit']);

        if ($request->has('facility_id')) {
            $query->whereHas('person', function($q) use ($request) {
                $q->where('facility_id', $request->facility_id);
            });
        }
        if ($request->has('sub_unit_id')) {
            $query->where('sub_unit_id', $request->sub_unit_id);
        }

        $profiles = $query->get();

        if ($request->wantsJson()) {
            return response()->json($profiles);
        }

        return view('admin.resident-profiles.index', compact('profiles'));
    }

    public function create()
    {
        $people = Person::all();
        $subUnits = SubUnit::all();
        $facilities = Facility::all();
        return view('admin.resident-profiles.create', compact('people', 'subUnits', 'facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:people,id',
            'sub_unit_id' => 'required|exists:sub_units,id',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date|after_or_equal:lease_start',
            'is_owner' => 'nullable|boolean',
        ]);

        $existing = ResidentProfile::where('person_id', $validated['person_id'])->first();
        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'This person already has a resident profile.'], 422);
            }
            return redirect()->back()->with('error', 'This person already has a resident profile.');
        }

        $profile = ResidentProfile::create($validated);

        $subUnit = SubUnit::find($validated['sub_unit_id']);
        $person = Person::find($validated['person_id']);
        if ($person && $subUnit) {
            $person->facility_id = $subUnit->facility_id;
            $person->save();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Resident profile created successfully!',
                'profile' => $profile->load(['person', 'subUnit'])
            ], 201);
        }

        return redirect()->route('admin.resident-profiles.index')
            ->with('success', 'Resident profile created successfully!');
    }

    public function show(ResidentProfile $residentProfile)
    {
        return response()->json($residentProfile->load(['person', 'subUnit.facility']));
    }

    public function edit(ResidentProfile $residentProfile)
    {
        $people = Person::all();
        $subUnits = SubUnit::all();
        $facilities = Facility::all();
        return view('admin.resident-profiles.edit', compact('residentProfile', 'people', 'subUnits', 'facilities'));
    }

    public function update(Request $request, ResidentProfile $residentProfile)
    {
        $validated = $request->validate([
            'person_id' => 'sometimes|exists:people,id',
            'sub_unit_id' => 'sometimes|exists:sub_units,id',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date|after_or_equal:lease_start',
            'is_owner' => 'nullable|boolean',
        ]);

        $residentProfile->update($validated);

        if (isset($validated['sub_unit_id'])) {
            $subUnit = SubUnit::find($validated['sub_unit_id']);
            $person = $residentProfile->person;
            if ($person && $subUnit) {
                $person->facility_id = $subUnit->facility_id;
                $person->save();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Resident profile updated successfully!',
                'profile' => $residentProfile
            ]);
        }

        return redirect()->route('admin.resident-profiles.index')
            ->with('success', 'Resident profile updated successfully!');
    }

    public function destroy(ResidentProfile $residentProfile)
    {
        $residentProfile->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Resident profile deleted successfully!']);
        }

        return redirect()->route('admin.resident-profiles.index')
            ->with('success', 'Resident profile deleted successfully!');
    }

    public function getBySubUnit($subUnitId)
    {
        $residents = ResidentProfile::where('sub_unit_id', $subUnitId)
            ->with(['person', 'person.vehicles'])
            ->get();

        return response()->json($residents);
    }
}