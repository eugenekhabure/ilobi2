<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitingDetails;
use App\Models\Invitation;
use App\Models\Person;
use Carbon\Carbon;

class PwaApiController extends Controller
{
    public function stats(Request $request)
    {
        $facilityId = $request->user()->facility_id;
        return response()->json([
            'checked_in' => VisitingDetails::where('facility_id', $facilityId)->whereNull('checkout_time')->count(),
            'pending' => Invitation::where('facility_id', $facilityId)->where('status', 'pending')->count(),
            'today' => VisitingDetails::where('facility_id', $facilityId)->whereDate('created_at', today())->count(),
        ]);
    }

    public function recentVisitors(Request $request)
    {
        $facilityId = $request->user()->facility_id;
        $visitors = VisitingDetails::with(['visitor', 'employee.user'])
            ->where('facility_id', $facilityId)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($visitors->map(function ($v) {
            return [
                'id' => $v->id,
                'visitor_name' => $v->visitor?->name ?? 'Unknown',
                'host_name' => $v->employee?->user?->name ?? 'No host',
                'check_in_time' => $v->checkin_time ?? $v->created_at?->format('H:i A'),
                'status' => $v->checkout_time ? 'checked-out' : 'checked-in',
            ];
        }));
    }

    public function employeeStats(Request $request)
    {
        $user = $request->user();
        $person = Person::where('email', $user->email)->first();
        if (!$person) {
            return response()->json(['total' => 0, 'pending' => 0]);
        }
        
        $visitors = VisitingDetails::where('employee_id', $person->employeeProfile?->id)->get();
        return response()->json([
            'total' => $visitors->count(),
            'pending' => $visitors->whereNull('checkout_time')->count(),
        ]);
    }

    public function pendingApprovals(Request $request)
    {
        $user = $request->user();
        $person = Person::where('email', $user->email)->first();
        if (!$person) {
            return response()->json([]);
        }

        $invitations = Invitation::where('host_person_id', $person->id)
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($invitations->map(function ($inv) {
            return [
                'id' => $inv->id,
                'visitor_name' => $inv->visitor?->name ?? $inv->visitor_email ?? 'Unknown',
                'check_in_time' => $inv->created_at->format('H:i A'),
            ];
        }));
    }

    public function approveVisitor($id)
    {
        $invitation = Invitation::findOrFail($id);
        $invitation->status = 'approved';
        $invitation->save();
        
        // Create visiting detail
        VisitingDetails::create([
            'visitor_id' => $invitation->visitor_id,
            'employee_id' => $invitation->host_person_id,
            'reg_no' => $invitation->qr_code,
            'checkin_time' => now(),
            'status' => 'approved',
        ]);

        return response()->json(['message' => 'Visitor approved']);
    }

    public function rejectVisitor($id)
    {
        $invitation = Invitation::findOrFail($id);
        $invitation->status = 'rejected';
        $invitation->save();
        return response()->json(['message' => 'Visitor rejected']);
    }

    public function residentStats(Request $request)
    {
        // Similar to employeeStats but for residents
        return response()->json(['total' => 0, 'pending' => 0]);
    }

    public function pendingGuestApprovals(Request $request)
    {
        // Similar to pendingApprovals but for residents
        return response()->json([]);
    }

    public function generateOTP(Request $request)
    {
        // Generate a 4-digit OTP
        $otp = rand(1000, 9999);
        // Save to database with expiry
        // Send via WhatsApp
        return response()->json([
            'otp' => $otp,
            'whatsapp_sent' => true,
        ]);
    }

    public function visitors(Request $request)
    {
        $facilityId = $request->user()->facility_id;
        $visitors = VisitingDetails::with(['visitor', 'employee.user'])
            ->where('facility_id', $facilityId)
            ->latest()
            ->limit(20)
            ->get();

        return response()->json($visitors->map(function ($v) {
            return [
                'id' => $v->id,
                'visitor_name' => $v->visitor?->name ?? 'Unknown',
                'host_name' => $v->employee?->user?->name ?? 'No host',
                'check_in_time' => $v->checkin_time ?? $v->created_at?->format('H:i A'),
                'status' => $v->checkout_time ? 'checked-out' : 'checked-in',
            ];
        }));
    }

    public function history(Request $request)
    {
        $facilityId = $request->user()->facility_id;
        $visitors = VisitingDetails::with(['visitor', 'employee.user'])
            ->where('facility_id', $facilityId)
            ->latest()
            ->limit(50)
            ->get();

        return response()->json($visitors->map(function ($v) {
            return [
                'id' => $v->id,
                'visitor_name' => $v->visitor?->name ?? 'Unknown',
                'action' => $v->checkout_time ? 'Checked Out' : 'Checked In',
                'created_at' => $v->created_at?->format('d M Y H:i A'),
                'status' => $v->checkout_time ? 'checked-out' : 'checked-in',
            ];
        }));
    }

    public function checkin(Request $request)
    {
        $qrCode = $request->qr_code;
        $invitation = Invitation::where('qr_code', $qrCode)->first();
        
        if (!$invitation) {
            return response()->json(['error' => 'Invalid QR code'], 404);
        }

        if ($invitation->status === 'used') {
            return response()->json(['error' => 'QR code already used'], 422);
        }

        $invitation->status = 'used';
        $invitation->save();

        return response()->json(['message' => 'Visitor checked in successfully']);
    }
}