<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Person;
use App\Models\Visitor;
use App\Models\SubUnit;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $query = Invitation::with(['facility', 'host', 'visitor', 'subUnit']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        if ($request->has('host_person_id')) {
            $query->where('host_person_id', $request->host_person_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invitations = $query->orderBy('created_at', 'desc')->get();

        if ($request->wantsJson()) {
            return response()->json($invitations);
        }

        return view('admin.invitations.index', compact('invitations'));
    }

    public function create()
    {
        $facilities = Facility::all();
        $people = Person::all();
        $subUnits = SubUnit::all();
        return view('admin.invitations.create', compact('facilities', 'people', 'subUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'host_person_id' => 'required|exists:people,id',
            'visitor_id' => 'nullable|exists:visitors,id',
            'visitor_email' => 'nullable|email',
            'visitor_phone' => 'nullable|string|max:20',
            'sub_unit_id' => 'nullable|exists:sub_units,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $host = Person::find($validated['host_person_id']);
        if ($host->facility_id != $validated['facility_id']) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'The host does not belong to this facility.'], 422);
            }
            return redirect()->back()->with('error', 'The host does not belong to this facility.');
        }

        $qrCode = Str::random(32);
        while (Invitation::where('qr_code', $qrCode)->exists()) {
            $qrCode = Str::random(32);
        }

        $invitation = Invitation::create([
            'facility_id' => $validated['facility_id'],
            'host_person_id' => $validated['host_person_id'],
            'visitor_id' => $validated['visitor_id'] ?? null,
            'visitor_email' => $validated['visitor_email'] ?? null,
            'visitor_phone' => $validated['visitor_phone'] ?? null,
            'sub_unit_id' => $validated['sub_unit_id'] ?? null,
            'qr_code' => $qrCode,
            'status' => 'pending',
            'expires_at' => $validated['expires_at'] ?? now()->addDays(1),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Invitation created successfully!',
                'invitation' => $invitation->load(['facility', 'host', 'visitor', 'subUnit']),
                'qr_code_url' => url('/api/v1/invitations/verify/' . $qrCode)
            ], 201);
        }

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Invitation created successfully!');
    }

    public function show(Invitation $invitation)
    {
        return response()->json($invitation->load(['facility', 'host', 'visitor', 'subUnit']));
    }

    public function edit(Invitation $invitation)
    {
        $facilities = Facility::all();
        $people = Person::all();
        $subUnits = SubUnit::all();
        return view('admin.invitations.edit', compact('invitation', 'facilities', 'people', 'subUnits'));
    }

    public function update(Request $request, Invitation $invitation)
    {
        $validated = $request->validate([
            'visitor_email' => 'nullable|email',
            'visitor_phone' => 'nullable|string|max:20',
            'sub_unit_id' => 'nullable|exists:sub_units,id',
            'expires_at' => 'nullable|date|after:now',
            'status' => 'sometimes|in:pending,checked_in,checked_out,expired',
        ]);

        $invitation->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Invitation updated successfully!',
                'invitation' => $invitation
            ]);
        }

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Invitation updated successfully!');
    }

    public function verify($qrCode)
    {
        $invitation = Invitation::where('qr_code', $qrCode)
            ->with(['facility', 'host', 'visitor', 'subUnit'])
            ->first();

        if (!$invitation) {
            return response()->json(['error' => 'Invalid QR code.'], 404);
        }

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->status = 'expired';
            $invitation->save();
            return response()->json(['error' => 'This invitation has expired.', 'invitation' => $invitation], 410);
        }

        if ($invitation->status === 'checked_in') {
            return response()->json(['error' => 'Already checked in.'], 422);
        }
        if ($invitation->status === 'checked_out') {
            return response()->json(['error' => 'Already checked out.'], 422);
        }

        return response()->json(['message' => 'Invitation is valid.', 'invitation' => $invitation]);
    }

    public function checkIn(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $invitation = Invitation::where('qr_code', $request->qr_code)->first();

        if (!$invitation) {
            return response()->json(['error' => 'Invalid QR code.'], 404);
        }

        if ($invitation->status === 'checked_in') {
            return response()->json(['error' => 'Already checked in.'], 422);
        }
        if ($invitation->status === 'checked_out') {
            return response()->json(['error' => 'Already checked out.'], 422);
        }
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->status = 'expired';
            $invitation->save();
            return response()->json(['error' => 'This invitation has expired.'], 410);
        }

        $invitation->status = 'checked_in';
        $invitation->checked_in_at = now();
        $invitation->save();

        $invitation->facility->accessLogs()->create([
            'loggable_type' => 'App\Models\Visitor',
            'loggable_id' => $invitation->visitor_id ?? null,
            'action' => 'check_in',
            'performed_by' => auth()->id(),
            'details' => [
                'invitation_id' => $invitation->id,
                'qr_code' => $invitation->qr_code,
                'host' => $invitation->host->full_name ?? null,
                'destination' => $invitation->subUnit?->name
            ]
        ]);

        return response()->json([
            'message' => 'Checked in successfully!',
            'invitation' => $invitation->load(['host', 'visitor', 'subUnit'])
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $invitation = Invitation::where('qr_code', $request->qr_code)->first();

        if (!$invitation) {
            return response()->json(['error' => 'Invalid QR code.'], 404);
        }

        if ($invitation->status !== 'checked_in') {
            return response()->json(['error' => 'Not checked in.'], 422);
        }

        $invitation->status = 'checked_out';
        $invitation->checked_out_at = now();
        $invitation->save();

        $invitation->facility->accessLogs()->create([
            'loggable_type' => 'App\Models\Visitor',
            'loggable_id' => $invitation->visitor_id ?? null,
            'action' => 'check_out',
            'performed_by' => auth()->id(),
            'details' => [
                'invitation_id' => $invitation->id,
                'qr_code' => $invitation->qr_code
            ]
        ]);

        return response()->json([
            'message' => 'Checked out successfully!',
            'invitation' => $invitation->load(['host', 'visitor', 'subUnit'])
        ]);
    }

    public function destroy(Invitation $invitation)
    {
        $invitation->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Invitation deleted successfully!']);
        }

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Invitation deleted successfully!');
    }

    public function getByHost($personId)
    {
        $invitations = Invitation::where('host_person_id', $personId)
            ->with(['facility', 'visitor', 'subUnit'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($invitations);
    }

    public function getStats($facilityId)
    {
        $stats = [
            'total' => Invitation::where('facility_id', $facilityId)->count(),
            'pending' => Invitation::where('facility_id', $facilityId)->where('status', 'pending')->count(),
            'checked_in' => Invitation::where('facility_id', $facilityId)->where('status', 'checked_in')->count(),
            'checked_out' => Invitation::where('facility_id', $facilityId)->where('status', 'checked_out')->count(),
            'expired' => Invitation::where('facility_id', $facilityId)->where('status', 'expired')->count(),
            'today' => Invitation::where('facility_id', $facilityId)
                ->whereDate('created_at', today())
                ->count(),
        ];

        return response()->json($stats);
    }
}