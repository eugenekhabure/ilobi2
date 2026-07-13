<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnomalyAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AnomalyAlertController extends Controller
{
    /**
     * Display a listing of anomaly alerts.
     */
    public function index()
    {
        return view('admin.anomaly-alerts.index');
    }

    /**
     * Get anomaly alerts data for DataTables.
     */
    public function getAnomalyAlerts(Request $request)
    {
        if ($request->ajax()) {
            $data = AnomalyAlert::with(['acknowledgedBy', 'resolvedBy'])
                ->orderBy('occurred_at', 'desc');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('severity_badge', function ($row) {
                    $colors = [
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'success'
                    ];
                    return '<span class="badge badge-' . ($colors[$row->severity] ?? 'secondary') . '">' . ucfirst($row->severity) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $colors = [
                        'new' => 'danger',
                        'acknowledged' => 'warning',
                        'investigating' => 'info',
                        'resolved' => 'success',
                        'false_alarm' => 'secondary'
                    ];
                    return '<span class="badge badge-' . ($colors[$row->status] ?? 'secondary') . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('type_label', function ($row) {
                    return ucfirst(str_replace('_', ' ', $row->type));
                })
                ->addColumn('time_elapsed', function ($row) {
                    return $row->occurred_at->diffForHumans();
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    
                    if ($row->status === 'new') {
                        $btn .= '<a href="javascript:void(0)" class="btn btn-warning btn-sm acknowledge-alert" data-id="' . $row->id . '" title="Acknowledge">Acknowledge</a> ';
                    }
                    
                    if (in_array($row->status, ['new', 'acknowledged', 'investigating'])) {
                        $btn .= '<a href="javascript:void(0)" class="btn btn-success btn-sm resolve-alert" data-id="' . $row->id . '" title="Resolve">Resolve</a> ';
                    }
                    
                    if ($row->status !== 'false_alarm') {
                        $btn .= '<a href="javascript:void(0)" class="btn btn-secondary btn-sm false-alarm" data-id="' . $row->id . '" title="Mark as False Alarm">False Alarm</a> ';
                    }
                    
                    $btn .= '<a href="' . route('admin.anomaly-alerts.show', $row->id) . '" class="btn btn-info btn-sm" title="View Details">View</a> ';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-alert" data-id="' . $row->id . '" title="Delete">Delete</a>';
                    
                    return $btn;
                })
                ->rawColumns(['severity_badge', 'status_badge', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new anomaly alert.
     */
    public function create()
    {
        return view('admin.anomaly-alerts.create');
    }

    /**
     * Store a newly created anomaly alert in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:unusual_time,unusual_location,unusual_frequency,unauthorized_access,tailgating,forced_entry,suspicious_behavior,system_anomaly',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'occurred_at' => 'required|date',
            'related_id' => 'nullable|integer',
            'related_type' => 'nullable|string|max:255',
            'metadata' => 'nullable|json',
        ]);

        $anomalyAlert = AnomalyAlert::create([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'severity' => $request->severity,
            'status' => 'new',
            'related_id' => $request->related_id,
            'related_type' => $request->related_type,
            'metadata' => $request->metadata,
            'occurred_at' => $request->occurred_at,
        ]);

        return redirect()->route('admin.anomaly-alerts.index')
            ->with('success', 'Anomaly alert created successfully!');
    }

    /**
     * Display the specified anomaly alert.
     */
    public function show($id)
    {
        $anomalyAlert = AnomalyAlert::with(['acknowledgedBy', 'resolvedBy'])->findOrFail($id);
        return view('admin.anomaly-alerts.show', compact('anomalyAlert'));
    }

    /**
     * Show the form for editing the specified anomaly alert.
     */
    public function edit($id)
    {
        $anomalyAlert = AnomalyAlert::findOrFail($id);
        return view('admin.anomaly-alerts.edit', compact('anomalyAlert'));
    }

    /**
     * Update the specified anomaly alert in storage.
     */
    public function update(Request $request, $id)
    {
        $anomalyAlert = AnomalyAlert::findOrFail($id);

        $request->validate([
            'type' => 'required|in:unusual_time,unusual_location,unusual_frequency,unauthorized_access,tailgating,forced_entry,suspicious_behavior,system_anomaly',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:new,acknowledged,investigating,resolved,false_alarm',
            'occurred_at' => 'required|date',
            'related_id' => 'nullable|integer',
            'related_type' => 'nullable|string|max:255',
            'metadata' => 'nullable|json',
            'resolution_notes' => 'nullable|string',
        ]);

        $anomalyAlert->update([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'severity' => $request->severity,
            'status' => $request->status,
            'related_id' => $request->related_id,
            'related_type' => $request->related_type,
            'metadata' => $request->metadata,
            'occurred_at' => $request->occurred_at,
            'resolution_notes' => $request->resolution_notes,
        ]);

        if ($request->status === 'acknowledged' && !$anomalyAlert->acknowledged_at) {
            $anomalyAlert->update([
                'acknowledged_at' => now(),
                'acknowledged_by' => Auth::id(),
            ]);
        }

        if ($request->status === 'resolved' && !$anomalyAlert->resolved_at) {
            $anomalyAlert->update([
                'resolved_at' => now(),
                'resolved_by' => Auth::id(),
            ]);
        }

        return redirect()->route('admin.anomaly-alerts.index')
            ->with('success', 'Anomaly alert updated successfully!');
    }

    /**
     * Remove the specified anomaly alert from storage.
     */
    public function destroy($id)
    {
        $anomalyAlert = AnomalyAlert::findOrFail($id);
        $anomalyAlert->delete();

        return response()->json(['success' => 'Anomaly alert deleted successfully!']);
    }

    /**
     * Acknowledge an anomaly alert.
     */
    public function acknowledge($id)
    {
        $anomalyAlert = AnomalyAlert::findOrFail($id);
        
        $anomalyAlert->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
            'acknowledged_by' => Auth::id(),
        ]);

        return response()->json(['success' => 'Anomaly alert acknowledged!']);
    }

    /**
     * Resolve an anomaly alert.
     */
    public function resolve(Request $request, $id)
    {
        $anomalyAlert = AnomalyAlert::findOrFail($id);

        $request->validate([
            'resolution_notes' => 'nullable|string',
        ]);
        
        $anomalyAlert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        return response()->json(['success' => 'Anomaly alert resolved!']);
    }

    /**
     * Mark an anomaly alert as false alarm.
     */
    public function falseAlarm($id)
    {
        $anomalyAlert = AnomalyAlert::findOrFail($id);
        
        $anomalyAlert->update([
            'status' => 'false_alarm',
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
            'resolution_notes' => 'Marked as false alarm',
        ]);

        return response()->json(['success' => 'Anomaly alert marked as false alarm!']);
    }

    /**
     * Get dashboard statistics for anomaly alerts.
     */
    public function getStats()
    {
        $stats = [
            'total' => AnomalyAlert::count(),
            'new' => AnomalyAlert::where('status', 'new')->count(),
            'acknowledged' => AnomalyAlert::where('status', 'acknowledged')->count(),
            'investigating' => AnomalyAlert::where('status', 'investigating')->count(),
            'resolved' => AnomalyAlert::where('status', 'resolved')->count(),
            'false_alarm' => AnomalyAlert::where('status', 'false_alarm')->count(),
            'critical' => AnomalyAlert::where('severity', 'critical')->whereIn('status', ['new', 'acknowledged', 'investigating'])->count(),
            'high' => AnomalyAlert::where('severity', 'high')->whereIn('status', ['new', 'acknowledged', 'investigating'])->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent anomaly alerts for dashboard.
     */
    public function getRecent()
    {
        $alerts = AnomalyAlert::with(['acknowledgedBy', 'resolvedBy'])
            ->whereIn('status', ['new', 'acknowledged', 'investigating'])
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($alerts);
    }
}