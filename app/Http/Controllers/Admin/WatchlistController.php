<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WatchlistController extends Controller
{
    /**
     * Display a listing of the watchlist.
     */
    public function index()
    {
        return view('admin.watchlist.index');
    }

    /**
     * Get watchlist data for DataTables.
     */
    public function getWatchlist(Request $request)
    {
        if ($request->ajax()) {
            $data = Watchlist::with(['addedBy', 'resolvedBy'])->orderBy('created_at', 'desc');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('added_by_name', function ($row) {
                    return $row->addedBy ? $row->addedBy->name : 'N/A';
                })
                ->addColumn('priority_badge', function ($row) {
                    $colors = [
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'success'
                    ];
                    return '<span class="badge badge-' . ($colors[$row->priority] ?? 'secondary') . '">' . ucfirst($row->priority) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $colors = [
                        'active' => 'warning',
                        'resolved' => 'success',
                        'archived' => 'secondary'
                    ];
                    return '<span class="badge badge-' . ($colors[$row->status] ?? 'secondary') . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('type_badge', function ($row) {
                    return '<span class="badge badge-info">' . ucfirst($row->type) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->status === 'active') {
                        $btn .= '<a href="javascript:void(0)" class="btn btn-success btn-sm resolve-watchlist" data-id="' . $row->id . '" title="Mark as Resolved">Resolve</a> ';
                    }
                    $btn .= '<a href="' . route('admin.watchlist.show', $row->id) . '" class="btn btn-info btn-sm" title="View Details">View</a> ';
                    $btn .= '<a href="' . route('admin.watchlist.edit', $row->id) . '" class="btn btn-primary btn-sm" title="Edit">Edit</a> ';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-watchlist" data-id="' . $row->id . '" title="Delete">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['priority_badge', 'status_badge', 'type_badge', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new watchlist entry.
     */
    public function create()
    {
        return view('admin.watchlist.create');
    }

    /**
     * Store a newly created watchlist entry in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'type' => 'required|in:visitor,employee,resident,contractor',
            'priority' => 'required|in:low,medium,high,critical',
            'reason' => 'required|string',
            'description' => 'nullable|string',
            'actions_taken' => 'nullable|string',
        ]);

        $watchlist = Watchlist::create([
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'id_number' => $request->id_number,
            'type' => $request->type,
            'priority' => $request->priority,
            'reason' => $request->reason,
            'description' => $request->description,
            'actions_taken' => $request->actions_taken,
            'watchlist_date' => now(),
            'status' => 'active',
            'added_by' => Auth::id(),
        ]);

        return redirect()->route('admin.watchlist.index')
            ->with('success', 'Person added to watchlist successfully!');
    }

    /**
     * Display the specified watchlist entry.
     */
    public function show($id)
    {
        $watchlist = Watchlist::with(['addedBy', 'resolvedBy'])->findOrFail($id);
        return view('admin.watchlist.show', compact('watchlist'));
    }

    /**
     * Show the form for editing the specified watchlist entry.
     */
    public function edit($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        return view('admin.watchlist.edit', compact('watchlist'));
    }

    /**
     * Update the specified watchlist entry in storage.
     */
    public function update(Request $request, $id)
    {
        $watchlist = Watchlist::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'type' => 'required|in:visitor,employee,resident,contractor',
            'priority' => 'required|in:low,medium,high,critical',
            'reason' => 'required|string',
            'description' => 'nullable|string',
            'actions_taken' => 'nullable|string',
            'status' => 'required|in:active,resolved,archived',
        ]);

        $watchlist->update([
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'id_number' => $request->id_number,
            'type' => $request->type,
            'priority' => $request->priority,
            'reason' => $request->reason,
            'description' => $request->description,
            'actions_taken' => $request->actions_taken,
            'status' => $request->status,
        ]);

        if ($request->status === 'resolved') {
            $watchlist->update([
                'resolved_by' => Auth::id(),
            ]);
        }

        return redirect()->route('admin.watchlist.index')
            ->with('success', 'Watchlist entry updated successfully!');
    }

    /**
     * Remove the specified watchlist entry from storage.
     */
    public function destroy($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        $watchlist->delete();

        return response()->json(['success' => 'Watchlist entry deleted successfully!']);
    }

    /**
     * Resolve a watchlist entry (mark as resolved).
     */
    public function resolve($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        
        $watchlist->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id(),
        ]);

        return response()->json(['success' => 'Watchlist entry marked as resolved!']);
    }

    /**
     * Get high priority watchlist entries.
     */
    public function getHighPriority()
    {
        $watchlist = Watchlist::where('status', 'active')
            ->whereIn('priority', ['high', 'critical'])
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json($watchlist);
    }
}