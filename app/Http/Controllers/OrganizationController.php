<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations.
     */
    public function index()
    {
        $organizations = Organization::all();
        return response()->json($organizations);
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        // For web views - we'll handle this later
    }

    /**
     * Store a newly created organization in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organizations,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subscription_plan' => 'nullable|string|default:trial',
        ]);

        $organization = Organization::create($validated);

        return response()->json([
            'message' => 'Organization created successfully!',
            'organization' => $organization
        ], 201);
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization)
    {
        return response()->json($organization->load('facilities'));
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:organizations,email,' . $organization->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subscription_plan' => 'nullable|string',
        ]);

        $organization->update($validated);

        return response()->json([
            'message' => 'Organization updated successfully!',
            'organization' => $organization
        ]);
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return response()->json([
            'message' => 'Organization deleted successfully!'
        ]);
    }
}