<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceCategory;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only Client Admin can manage categories
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->organization_id) {
                abort(403, 'Only Client Admin can manage maintenance categories.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of maintenance categories.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $categories = MaintenanceCategory::where('facility_id', $facilityId)
            ->orderBy('name')
            ->paginate(20);

        return view('admin.maintenance.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $icons = [
            'plumbing' => '🔧 Plumbing',
            'electrical' => '⚡ Electrical',
            'cleaning' => '🧹 Cleaning',
            'security' => '🛡️ Security',
            'hvac' => '❄️ HVAC',
            'furniture' => '🪑 Furniture',
            'pest_control' => '🐜 Pest Control',
            'other' => '🔨 Other',
        ];

        return view('admin.maintenance.categories.create', compact('icons'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $request->validate([
            'name' => 'required|string|max:255|unique:maintenance_categories,name,NULL,id,facility_id,' . $facilityId,
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $category = MaintenanceCategory::create([
            'facility_id' => $facilityId,
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('admin.maintenance-categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing a category.
     */
    public function edit($id)
    {
        $facilityId = Auth::user()->facility_id;

        $category = MaintenanceCategory::where('facility_id', $facilityId)
            ->findOrFail($id);

        $icons = [
            'plumbing' => '🔧 Plumbing',
            'electrical' => '⚡ Electrical',
            'cleaning' => '🧹 Cleaning',
            'security' => '🛡️ Security',
            'hvac' => '❄️ HVAC',
            'furniture' => '🪑 Furniture',
            'pest_control' => '🐜 Pest Control',
            'other' => '🔨 Other',
        ];

        return view('admin.maintenance.categories.edit', compact('category', 'icons'));
    }

    /**
     * Update a category.
     */
    public function update(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $category = MaintenanceCategory::where('facility_id', $facilityId)
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:maintenance_categories,name,' . $id . ',id,facility_id,' . $facilityId,
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.maintenance-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a category.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $category = MaintenanceCategory::where('facility_id', $facilityId)
            ->findOrFail($id);

        // Check if category has any requests
        if ($category->requests()->count() > 0) {
            return redirect()->route('admin.maintenance-categories.index')
                ->with('error', 'Cannot delete category with existing maintenance requests. Deactivate it instead.');
        }

        $category->delete();

        return redirect()->route('admin.maintenance-categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus($id)
    {
        $facilityId = Auth::user()->facility_id;

        $category = MaintenanceCategory::where('facility_id', $facilityId)
            ->findOrFail($id);

        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return redirect()->route('admin.maintenance-categories.index')
            ->with('success', 'Category status updated!');
    }
}