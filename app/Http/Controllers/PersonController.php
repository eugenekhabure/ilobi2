<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Facility;
use App\Models\EmployeeProfile;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::query();

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        if ($request->has('type')) {
            if ($request->type === 'employee') {
                $query->has('employeeProfile');
            } elseif ($request->type === 'resident') {
                $query->has('residentProfile');
            }
        }

        $people = $query->with(['facility', 'employeeProfile', 'residentProfile'])->get();

        if ($request->wantsJson()) {
            return response()->json($people);
        }

        return view('admin.people.index', compact('people'));
    }

    public function create()
    {
        $facilities = Facility::all();
        return view('admin.people.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:people,email',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|string',
            'notes' => 'nullable|string',
            'employee_code' => 'nullable|string|unique:employee_profiles,employee_code',
            'occupant_id' => 'nullable|exists:occupants,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'sub_unit_id' => 'nullable|exists:sub_units,id',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date',
            'is_owner' => 'nullable|boolean',
        ]);

        $person = Person::create([
            'facility_id' => $validated['facility_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'photo' => $validated['photo'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $facility = Facility::find($validated['facility_id']);

        if (in_array($facility->type, ['corporate', 'commercial'])) {
            EmployeeProfile::create([
                'person_id' => $person->id,
                'employee_code' => $validated['employee_code'] ?? null,
                'occupant_id' => $validated['occupant_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'hire_date' => $validated['hire_date'] ?? null,
            ]);
        }

        if ($facility->type === 'residential') {
            ResidentProfile::create([
                'person_id' => $person->id,
                'sub_unit_id' => $validated['sub_unit_id'] ?? null,
                'lease_start' => $validated['lease_start'] ?? null,
                'lease_end' => $validated['lease_end'] ?? null,
                'is_owner' => $validated['is_owner'] ?? false,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Person created successfully!',
                'person' => $person->load(['facility', 'employeeProfile', 'residentProfile'])
            ], 201);
        }

        return redirect()->route('admin.people.index')
            ->with('success', 'Person created successfully!');
    }

    public function show(Person $person)
    {
        return response()->json($person->load([
            'facility',
            'employeeProfile.occupant',
            'employeeProfile.department',
            'residentProfile.subUnit',
            'vehicles',
            'invitations',
            'deliveries',
            'accessLogs'
        ]));
    }

    public function edit(Person $person)
    {
        $facilities = Facility::all();
        return view('admin.people.edit', compact('person', 'facilities'));
    }

    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:people,email,' . $person->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|string',
            'notes' => 'nullable|string',
            'facility_id' => 'sometimes|exists:facilities,id',
        ]);

        $person->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Person updated successfully!',
                'person' => $person
            ]);
        }

        return redirect()->route('admin.people.index')
            ->with('success', 'Person updated successfully!');
    }

    public function destroy(Person $person)
    {
        if ($person->employeeProfile) {
            $person->employeeProfile->delete();
        }
        if ($person->residentProfile) {
            $person->residentProfile->delete();
        }

        $person->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Person deleted successfully!']);
        }

        return redirect()->route('admin.people.index')
            ->with('success', 'Person deleted successfully!');
    }

    public function getByType(Request $request)
    {
        $type = $request->query('type');
        $facilityId = $request->query('facility_id');

        $query = Person::where('facility_id', $facilityId);

        if ($type === 'employees') {
            $query->has('employeeProfile');
        } elseif ($type === 'residents') {
            $query->has('residentProfile');
        }

        $people = $query->with(['facility', 'employeeProfile', 'residentProfile'])->get();

        return response()->json($people);
    }
}