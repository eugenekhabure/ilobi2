<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use App\Models\Employee;
use App\Models\Visitor;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BlacklistController extends Controller
{
    /**
     * Display a listing of the blacklist.
     */
    public function index()
    {
        return view('admin.blacklist.index');
    }

    /**
     * Get blacklist data for DataTables.
     */
    public function getBlacklist(Request $request)
    {
        if ($request->ajax()) {
            $data = Blacklist::with(['addedBy', 'removedBy'])->orderBy('created_at', 'desc');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('added_by_name', function ($row) {
                    return $row->addedBy ? $row->addedBy->name : 'N/A';
                })
                ->addColumn('status_badge', function ($row) {
                    $colors = [
                        'active' => 'success',
                        'expired' => 'warning',
                        'removed' => 'danger'
                    ];
                    return '<span class="badge badge-' . ($colors[$row->status] ?? 'secondary') . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('type_badge', function ($row) {
                    return '<span class="badge badge-info">' . ucfirst($row->type) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->status === 'active') {
                        $btn .= '<a href="javascript:void(0)" class="btn btn-warning btn-sm remove-blacklist" data-id="' . $row->id . '" title="Remove from blacklist">Remove</a> ';
                    }
                    $btn .= '<a href="' . route('admin.blacklist.show', $row->id) . '" class="btn btn-info btn-sm" title="View Details">View</a> ';
                    $btn .= '<a href="' . route('admin.blacklist.edit', $row->id) . '" class="btn btn-primary btn-sm" title="Edit">Edit</a> ';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-blacklist" data-id="' . $row->id . '" title="Delete">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'type_badge', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new blacklist entry.
     */
    public function create()
    {
        return view('admin.blacklist.create');
    }

    /**
     * Store a newly created blacklist entry in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'type' => 'required|in:visitor,employee,resident,contractor',
            'reason' => 'required|string',
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $blacklist = Blacklist::create([
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'id_number' => $request->id_number,
            'type' => $request->type,
            'reason' => $request->reason,
            'description' => $request->description,
            'expiry_date' => $request->expiry_date,
            'blacklisted_date' => now(),
            'status' => 'active',
            'added_by' => Auth::id(),
        ]);

        return redirect()->route('admin.blacklist.index')
            ->with('success', 'Person added to blacklist successfully!');
    }

    /**
     * Display the specified blacklist entry.
     */
    public function show($id)
    {
        $blacklist = Blacklist::with(['addedBy', 'removedBy'])->findOrFail($id);
        return view('admin.blacklist.show', compact('blacklist'));
    }

    /**
     * Show the form for editing the specified blacklist entry.
     */
    public function edit($id)
    {
        $blacklist = Blacklist::findOrFail($id);
        return view('admin.blacklist.edit', compact('blacklist'));
    }

    /**
     * Update the specified blacklist entry in storage.
     */
    public function update(Request $request, $id)
    {
        $blacklist = Blacklist::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'type' => 'required|in:visitor,employee,resident,contractor',
            'reason' => 'required|string',
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,expired,removed',
        ]);

        $blacklist->update([
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'id_number' => $request->id_number,
            'type' => $request->type,
            'reason' => $request->reason,
            'description' => $request->description,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
        ]);

        if ($request->status === 'removed') {
            $blacklist->update([
                'removed_by' => Auth::id(),
                'removal_reason' => $request->removal_reason,
            ]);
        }

        return redirect()->route('admin.blacklist.index')
            ->with('success', 'Blacklist entry updated successfully!');
    }

    /**
     * Remove the specified blacklist entry from storage.
     */
    public function destroy($id)
    {
        $blacklist = Blacklist::findOrFail($id);
        $blacklist->delete();

        return response()->json(['success' => 'Blacklist entry deleted successfully!']);
    }

    /**
     * Remove a person from the blacklist (soft removal).
     */
    public function remove($id)
    {
        $blacklist = Blacklist::findOrFail($id);
        
        $blacklist->update([
            'status' => 'removed',
            'removed_by' => Auth::id(),
            'removal_reason' => 'Manually removed by admin',
        ]);

        return response()->json(['success' => 'Person removed from blacklist successfully!']);
    }

    /**
     * Check if a person is blacklisted.
     */
    public function check(Request $request)
    {
        $request->validate([
            'phone_number' => 'nullable|string',
            'id_number' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $query = Blacklist::where('status', 'active');

        if ($request->phone_number) {
            $query->where('phone_number', $request->phone_number);
        }
        if ($request->id_number) {
            $query->where('id_number', $request->id_number);
        }
        if ($request->email) {
            $query->where('email', $request->email);
        }

        $blacklist = $query->first();

        return response()->json([
            'blacklisted' => $blacklist ? true : false,
            'data' => $blacklist
        ]);
    }
}