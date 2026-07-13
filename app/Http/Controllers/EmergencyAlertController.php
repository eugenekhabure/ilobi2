<?php

namespace App\Http\Controllers;

use App\Models\EmergencyAlert;
use App\Models\AlertAcknowledgmentToken;
use App\Services\EmergencyAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyAlertController extends Controller
{
    protected $alertService;

    public function __construct(EmergencyAlertService $alertService)
    {
        $this->alertService = $alertService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of emergency alerts.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;
        
        $alerts = EmergencyAlert::where('facility_id', $facilityId)
            ->with(['creator', 'facility'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = $this->alertService->getStats($facilityId);

        return view('admin.emergency-alerts.index', compact('alerts', 'stats'));
    }

    /**
     * Show the form for creating a new emergency alert.
     */
    public function create()
    {
        // Only Client Admin can create alerts
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can send emergency alerts.');
        }

        $facility = Auth::user()->facility;
        $audienceOptions = [
            'residents' => 'Residents',
            'employees' => 'Employees',
            'security' => 'Security Team',
            'visitors' => 'Current Visitors',
        ];

        $severityOptions = [
            'warning' => '⚠️ Warning',
            'critical' => '🔴 Critical',
            'emergency' => '🚨 Emergency',
        ];

        return view('admin.emergency-alerts.create', compact('facility', 'audienceOptions', 'severityOptions'));
    }

    /**
     * Store a newly created emergency alert.
     */
    public function store(Request $request)
    {
        // Only Client Admin can create alerts
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can send emergency alerts.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'severity' => 'required|in:warning,critical,emergency',
            'target_audience' => 'required|array|min:1',
            'target_audience.*' => 'in:residents,employees,security,visitors',
            'expires_at' => 'nullable|date|after:now',
        ]);

        try {
            $alert = $this->alertService->sendAlert(
                Auth::user()->facility_id,
                Auth::id(),
                [
                    'title' => $request->title,
                    'message' => $request->message,
                    'severity' => $request->severity,
                    'target_audience' => $request->target_audience,
                    'expires_at' => $request->expires_at,
                ]
            );

            return redirect()->route('admin.emergency-alerts.show', $alert->id)
                ->with('success', 'Emergency alert sent successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to send alert: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified emergency alert.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;
        
        $alert = EmergencyAlert::where('facility_id', $facilityId)
            ->with(['creator', 'facility', 'recipients', 'acknowledgments'])
            ->findOrFail($id);

        $recipients = $alert->recipients()->paginate(20);

        return view('admin.emergency-alerts.show', compact('alert', 'recipients'));
    }

    /**
     * Acknowledge an emergency alert via token.
     */
    public function acknowledge($token)
    {
        $result = $this->alertService->acknowledgeAlert($token);

        if ($result['success']) {
            return view('emergency.acknowledged', ['message' => $result['message']]);
        }

        return view('emergency.acknowledged', ['error' => $result['message']]);
    }

    /**
     * Get alert statistics (API).
     */
    public function getStats(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;
        $stats = $this->alertService->getStats($facilityId);
        return response()->json($stats);
    }

    /**
     * Get active alerts (API).
     */
    public function getActiveAlerts(Request $request)
    {
        $facilityId = $request->facility_id ?? Auth::user()->facility_id;
        
        $alerts = EmergencyAlert::where('facility_id', $facilityId)
            ->active()
            ->with(['facility'])
            ->get();

        return response()->json($alerts);
    }
}