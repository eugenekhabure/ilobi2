<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $packages = DB::table('packages')->get();
        return view('admin.subscription.packages', compact('packages'));
    }
    
    public function purchase()
    {
        $packages = DB::table('packages')->get();
        return view('admin.subscription.purchase', compact('packages'));
    }
    public function purchase_request()
    {
        $data = DB::table('package_requests')->get();
        return view('admin.subscription.purchase_requests', compact('data'));
    }
    
    public function updateStatus(Request $request)
   {
    $request->validate([
        'id'     => 'required|exists:package_requests,id', 
        'status' => 'required|in:approved,rejected',   
    ]);

    $packageRequest = DB::table('package_requests')->where('id', $request->id)->first();

    if ($packageRequest) {
        DB::table('package_requests')
            ->where('id', $request->id)
            ->update([
                'status' => $request->status,                    
                'admin_approved' => now()                
            ]);

        return redirect()->back()->with('success', 'The package request status has been updated successfully.');
    } else {
        return redirect()->back()->with('error', 'Package request not found.');
    }
   }
public function purchase_store(Request $request)
{
    try {
        $request->validate([
            'package_id'         => 'required|exists:packages,id',
            'transaction_number' => 'required|unique:package_requests,transaction_number',
            'transaction_image'  => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'days'               => 'required|integer|min:1',
            'package_amount'     => 'required|numeric|min:0',
        ]);

        // Save image
        $imagePath = $request->file('transaction_image')->store('transactions', 'public');

        DB::table('package_requests')->insert([
            'package_id'         => $request->package_id,
            'transaction_number' => $request->transaction_number,
            'package_amount'     => $request->package_amount,
            'transaction_image'  => $imagePath,
            'status'             => 'pending',
            'created_at'         => now(),
            'userid'             => Auth::id(),
            'updated_at'         => now(),
            'days'               => $request->days,
        ]);

        return redirect()->back()->with('success', '✅ Your purchase request has been submitted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', '❌ Something went wrong: ' . $e->getMessage());
    }
}

   
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'package_name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'subscription_type' => 'required|in:daily,monthly,yearly',
            'enter_days' => 'required|integer|min:1',
            'status' => 'required|in:1,0',
        ]);

        DB::table('packages')->insert([
            'name' => $validatedData['package_name'],
            'mrp' => $validatedData['mrp'],
            'amount' => $validatedData['amount'],
            'type' => ucfirst($validatedData['subscription_type']),
            'days' => $validatedData['enter_days'],
            'status' => $validatedData['status'],
            'date' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Package created successfully!');
    }

public function update(Request $request)
{
    $validatedData = $request->validate([
        'id' => 'required|integer|exists:packages,id',
        'package_name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('packages', 'name')->ignore($request->id), // Ignore the current package ID
        ],
        'mrp' => 'required|numeric|min:0',
        'amount' => 'required|numeric|min:0',
        'subscription_type' => 'required|in:daily,monthly,yearly',
        'enter_days' => 'required|integer|min:1',
        'status' => 'required|in:1,0',
    ]);

    DB::table('packages')
        ->where('id', $validatedData['id'])
        ->update([
            'name' => $validatedData['package_name'],
            'mrp' => $validatedData['mrp'],
            'amount' => $validatedData['amount'],
            'type' => ucfirst($validatedData['subscription_type']),
            'days' => $validatedData['enter_days'],
            'status' => $validatedData['status'],
            'updated_at' => now(),
        ]);

    return redirect()->back()->with('success', 'Package updated successfully!');
}


}
