<?php

namespace App\Http\Controllers;

use App\Models\StaffDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffDepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of staff departments.
     */
    public function index()
    {
        $facilityId = Auth::user()->facility_id;

        $departments = StaffDepartment::where('facility_id', $facilityId)
            ->orderBy('name')
            ->paginate(20);

        return view('admin.staff-departments.index', compact('departments'));
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $request->validate([
            'name' => 'required|string|max:255|unique:staff_departments,name,NULL,id,facility_id,' . $facilityId,
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $department = StaffDepartment::create([
            'facility_id' => $facilityId,
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'department' => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'icon_html' => $department->icon_html,
                ]
            ]);
        }

        return redirect()->route('admin.staff-departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Get department data for editing.
     */
    public function edit($id)
    {
        $facilityId = Auth::user()->facility_id;

        $department = StaffDepartment::where('facility_id', $facilityId)
            ->findOrFail($id);

        return response()->json($department);
    }

    /**
     * Update a department.
     */
    public function update(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $department = StaffDepartment::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:staff_departments,name,' . $id . ',id,facility_id,' . $facilityId,
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $department->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.staff-departments.index')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Delete a department.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $department = StaffDepartment::where('facility_id', $facilityId)
            ->findOrFail($id);

        // Check if department has staff
        if ($department->staff()->count() > 0) {
            return redirect()->route('admin.staff-departments.index')
                ->with('error', 'Cannot delete department with existing staff members. Deactivate it instead.');
        }

        $department->delete();

        return redirect()->route('admin.staff-departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    /**
     * Toggle department status.
     */
    public function toggleStatus($id)
    {
        $facilityId = Auth::user()->facility_id;

        $department = StaffDepartment::where('facility_id', $facilityId)
            ->findOrFail($id);

        $department->update([
            'is_active' => !$department->is_active,
        ]);

        return redirect()->route('admin.staff-departments.index')
            ->with('success', 'Department status updated!');
    }
}