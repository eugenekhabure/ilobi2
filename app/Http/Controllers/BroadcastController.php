<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\BroadcastTemplate;
use App\Services\BroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastController extends Controller
{
    protected $broadcastService;

    public function __construct(BroadcastService $broadcastService)
    {
        $this->broadcastService = $broadcastService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of broadcasts.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $broadcasts = Broadcast::where('facility_id', $facilityId)
            ->with(['creator', 'facility'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = $this->broadcastService->getStats($facilityId);

        return view('admin.broadcasts.index', compact('broadcasts', 'stats'));
    }

    /**
     * Show the form for creating a new broadcast.
     */
    public function create()
    {
        // Only Client Admin can send broadcasts
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can send broadcasts.');
        }

        $templates = BroadcastTemplate::where('facility_id', Auth::user()->facility_id)
            ->where('is_active', true)
            ->get();

        $groupOptions = [
            'residents' => 'Residents',
            'employees' => 'Employees',
            'security' => 'Security Team',
            'visitors' => 'Current Visitors',
        ];

        $channelOptions = [
            'whatsapp' => 'WhatsApp',
            'sms' => 'SMS',
            'both' => 'Both (WhatsApp + SMS)',
        ];

        return view('admin.broadcasts.create', compact('templates', 'groupOptions', 'channelOptions'));
    }

    /**
     * Store a newly created broadcast.
     */
    public function store(Request $request)
    {
        // Only Client Admin can send broadcasts
        if (!Auth::user()->organization_id) {
            abort(403, 'Only Client Admin can send broadcasts.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_groups' => 'required|array|min:1',
            'target_groups.*' => 'in:residents,employees,security,visitors',
            'channel' => 'required|in:whatsapp,sms,both',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        try {
            $broadcast = $this->broadcastService->sendBroadcast(
                Auth::user()->facility_id,
                Auth::id(),
                [
                    'title' => $request->title,
                    'message' => $request->message,
                    'target_groups' => $request->target_groups,
                    'channel' => $request->channel,
                    'scheduled_at' => $request->scheduled_at,
                ]
            );

            return redirect()->route('admin.broadcasts.show', $broadcast->id)
                ->with('success', 'Broadcast sent successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to send broadcast: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified broadcast.
     */
    public function show($id)
    {
        $facilityId = Auth::user()->facility_id;

        $broadcast = Broadcast::where('facility_id', $facilityId)
            ->with(['creator', 'facility', 'recipients'])
            ->findOrFail($id);

        $recipients = $broadcast->recipients()->paginate(20);

        return view('admin.broadcasts.show', compact('broadcast', 'recipients'));
    }

    /**
     * Show the form for creating a broadcast from a template.
     */
    public function useTemplate($id)
    {
        $template = BroadcastTemplate::where('facility_id', Auth::user()->facility_id)
            ->findOrFail($id);

        return view('admin.broadcasts.create', [
            'template' => $template,
            'templates' => BroadcastTemplate::where('facility_id', Auth::user()->facility_id)
                ->where('is_active', true)
                ->get(),
            'groupOptions' => [
                'residents' => 'Residents',
                'employees' => 'Employees',
                'security' => 'Security Team',
                'visitors' => 'Current Visitors',
            ],
            'channelOptions' => [
                'whatsapp' => 'WhatsApp',
                'sms' => 'SMS',
                'both' => 'Both (WhatsApp + SMS)',
            ],
        ]);
    }

    /**
     * Delete a broadcast.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $broadcast = Broadcast::where('facility_id', $facilityId)->findOrFail($id);
        $broadcast->delete();

        return redirect()->route('admin.broadcasts.index')
            ->with('success', 'Broadcast deleted successfully!');
    }
}