<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of packages.
     */
    public function index()
    {
        $packages = DB::table('packages')->get();
        return view('admin.subscription.packages', compact('packages'));
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'package_name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'subscription_type' => 'required|in:daily,monthly,yearly',
            'days' => 'nullable|integer|min:1',
        ]);

        DB::table('packages')->insert([
            'package_name' => $validatedData['package_name'],
            'mrp' => $validatedData['mrp'],
            'amount' => $validatedData['amount'],
            'subscription_type' => $validatedData['subscription_type'],
            'days' => $validatedData['days'] ?? 30,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully!');
    }

    /**
     * Update the specified package in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'package_name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'subscription_type' => 'required|in:daily,monthly,yearly',
            'days' => 'nullable|integer|min:1',
        ]);

        DB::table('packages')
            ->where('id', $request->package_id)
            ->update([
                'package_name' => $request->package_name,
                'mrp' => $request->mrp,
                'amount' => $request->amount,
                'subscription_type' => $request->subscription_type,
                'days' => $request->days ?? 30,
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully!');
    }

    /**
     * Update package status (activate/deactivate).
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'status' => 'required|in:0,1',
        ]);

        DB::table('packages')
            ->where('id', $request->package_id)
            ->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package status updated!');
    }

    /**
     * Show the purchase form.
     */
    public function purchase()
    {
        $packages = DB::table('packages')->where('status', 1)->get();
        return view('admin.purchase', compact('packages'));
    }

    /**
     * Store a purchase request.
     */
    public function purchase_store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'transaction_number' => 'required|string|max:255',
            'transaction_image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('transaction_image')) {
            $imagePath = $request->file('transaction_image')->store('transactions', 'public');
        }

        DB::table('package_requests')->insert([
            'user_id' => Auth::id(),
            'package_id' => $request->package_id,
            'transaction_number' => $request->transaction_number,
            'transaction_image' => $imagePath,
            'status' => 'pending',
            'amount' => DB::table('packages')->where('id', $request->package_id)->value('amount'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('subscription.purchase')
            ->with('success', 'Purchase request submitted successfully!');
    }

    /**
     * Display all purchase requests.
     */
    public function purchase_request()
    {
        $requests = DB::table('package_requests')
            ->join('users', 'package_requests.user_id', '=', 'users.id')
            ->join('packages', 'package_requests.package_id', '=', 'packages.id')
            ->select(
                'package_requests.*',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as user_name"),
                'packages.package_name'
            )
            ->orderBy('package_requests.created_at', 'desc')
            ->get();

        return view('admin.purchase_requests', compact('requests'));
    }
}