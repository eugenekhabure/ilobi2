<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding wizard form.
     */
    public function index()
    {
        return view('admin.onboarding');
    }

    /**
     * Handle the onboarding form submission.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Organization
            'org_name' => 'required|string|max:255',
            'org_email' => 'required|email|unique:organizations,email',
            'org_phone' => 'nullable|string|max:20',
            'org_address' => 'nullable|string',

            // Facility
            'facility_name' => 'required|string|max:255',
            'facility_type' => 'required|in:corporate,commercial,residential,school,hospital,industrial',
            'facility_address' => 'nullable|string',
            'facility_city' => 'nullable|string|max:100',
            'facility_country' => 'nullable|string|max:100',

            // Admin User
            'admin_first_name' => 'required|string|max:255',
            'admin_last_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_username' => 'nullable|string|max:255|unique:users,username',
            'admin_password' => 'required|min:8|confirmed',
        ]);

        // 1. Create Organization
        $org = Organization::create([
            'name' => $request->org_name,
            'email' => $request->org_email,
            'phone' => $request->org_phone,
            'address' => $request->org_address,
            'subscription_plan' => 'trial',
        ]);

        // 2. Create Facility
        $facility = Facility::create([
            'organization_id' => $org->id,
            'name' => $request->facility_name,
            'type' => $request->facility_type,
            'address' => $request->facility_address,
            'city' => $request->facility_city,
            'country' => $request->facility_country,
            'is_active' => true,
        ]);

        // 3. Create Admin User
        // Generate username from email (or use provided one)
        $username = $request->admin_username ?? explode('@', $request->admin_email)[0];

        // Ensure username is unique
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'first_name' => $request->admin_first_name,
            'last_name' => $request->admin_last_name,
            'email' => $request->admin_email,
            'username' => $username,
            'password' => Hash::make($request->admin_password),
            'organization_id' => $org->id,
            'facility_id' => $facility->id,
        ]);

        // 4. Assign Super Admin Role (if Spatie Roles exist)
        try {
            $role = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
            if ($role) {
                $user->assignRole($role);
            }
        } catch (\Exception $e) {
            // Role system not set up yet, skip.
        }

        return redirect()->route('login')
            ->with('success', 'Organization, Facility, and Admin created successfully! You can now log in.');
    }
}