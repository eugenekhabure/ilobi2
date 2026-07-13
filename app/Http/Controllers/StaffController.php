<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffDepartment;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of staff.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $query = Staff::where('facility_id', $facilityId)
            ->with(['department', 'user']);

        // Filter by department
        if ($request->has('department_id') && $request->department_id != 'all') {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by emergency contact
        if ($request->has('emergency') && $request->emergency) {
            $query->where('is_emergency_contact', true);
        }

        $staff = $query->orderBy('first_name')->paginate(20);

        $departments = StaffDepartment::where('facility_id', $facilityId)
            ->where('is_active', true)
            ->get();

        $stats = [
            'total' => Staff::where('facility_id', $facilityId)->count(),
            'active' => Staff::where('facility_id', $facilityId)->where('status', 'active')->count(),
            'emergency' => Staff::where('facility_id', $facilityId)->where('is_emergency_contact', true)->count(),
            'departments' => StaffDepartment::where('facility_id', $facilityId)->count(),
        ];

        $statuses = [
            'all' => 'All Statuses',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
        ];

        return view('admin.staff.index', compact('staff', 'departments', 'stats', 'statuses'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        $facilityId = Auth::user()->facility_id;

        $departments = StaffDepartment::where('facility_id', $facilityId)
            ->where('is_active', true)
            ->get();

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
        ];

        return view('admin.staff.create', compact('departments', 'statuses'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:staff_departments,id',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'status' => 'required|in:active,inactive,on_leave',
            'is_emergency_contact' => 'nullable|boolean',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('staff', 'public');
        }

        $staff = Staff::create([
            'facility_id' => $facilityId,
            'department_id' => $request->department_id,
            'user_id' => null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'bio' => $request->bio,
            'photo' => $photoPath,
            'status' => $request->status,
            'is_emergency_contact' => $request->has('is_emergency_contact'),
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member added successfully!');
    }

    /**
     * Display the specified staff member.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;

        $staff = Staff::where('facility_id', $facilityId)
            ->with(['department', 'user'])
            ->findOrFail($id);

        return view('admin.staff.show', compact('staff'));
    }

    /**
     * Show the form for editing a staff member.
     */
    public function edit($id)
    {
        $facilityId = Auth::user()->facility_id;

        $staff = Staff::where('facility_id', $facilityId)
            ->findOrFail($id);

        $departments = StaffDepartment::where('facility_id', $facilityId)
            ->where('is_active', true)
            ->get();

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
        ];

        return view('admin.staff.edit', compact('staff', 'departments', 'statuses'));
    }

    /**
     * Update a staff member.
     */
    public function update(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $staff = Staff::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:staff_departments,id',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'status' => 'required|in:active,inactive,on_leave',
            'is_emergency_contact' => 'nullable|boolean',
        ]);

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'department_id' => $request->department_id,
            'bio' => $request->bio,
            'status' => $request->status,
            'is_emergency_contact' => $request->has('is_emergency_contact'),
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($staff->photo) {
                Storage::disk('public')->delete($staff->photo);
            }
            $updateData['photo'] = $request->file('photo')->store('staff', 'public');
        }

        $staff->update($updateData);

        return redirect()->route('admin.staff.show', $staff->id)
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Delete a staff member.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $staff = Staff::where('facility_id', $facilityId)
            ->findOrFail($id);

        // Delete photo
        if ($staff->photo) {
            Storage::disk('public')->delete($staff->photo);
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully!');
    }

    /**
     * Toggle emergency contact status.
     */
    public function toggleEmergency($id)
    {
        $facilityId = Auth::user()->facility_id;

        $staff = Staff::where('facility_id', $facilityId)
            ->findOrFail($id);

        $staff->update([
            'is_emergency_contact' => !$staff->is_emergency_contact,
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Emergency contact status updated!');
    }

    /**
     * Get staff for PWA.
     */
    public function getPwaStaff(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;

        $staff = Staff::where('facility_id', $facilityId)
            ->where('status', 'active')
            ->with(['department'])
            ->orderBy('first_name')
            ->get();

        return response()->json($staff);
    }

    /**
     * Get emergency contacts for PWA.
     */
    public function getEmergencyContacts(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;

        $emergency = Staff::where('facility_id', $facilityId)
            ->where('status', 'active')
            ->where('is_emergency_contact', true)
            ->with(['department'])
            ->orderBy('first_name')
            ->get();

        return response()->json($emergency);
    }
}