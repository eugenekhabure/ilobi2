<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Employee;
use App\Models\Invitation;
use App\Models\VisitingDetails;
use App\Models\Person;
use App\Models\PreRegister;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckInController extends Controller
{
    public function index()
    {
        return view('frontend.check-in.index');
    }

    public function scanQr()
    {
        return view('frontend.check-in.scan-qr');
    }

    public function createStepOne()
    {
        return view('frontend.check-in.step-one');
    }

    public function postCreateStepOne(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_phone' => 'required|string|max:20',
            'visitor_email' => 'nullable|email|max:255',
        ]);

        // Check if visitor already exists
        $visitor = Visitor::where('phone', $request->visitor_phone)->first();
        if (!$visitor) {
            $visitor = Visitor::create([
                'name' => $request->visitor_name,
                'phone' => $request->visitor_phone,
                'email' => $request->visitor_email,
                'reg_no' => 'VIS-' . time(),
            ]);
        }

        session(['visitor_id' => $visitor->id]);

        return redirect()->route('check-in.step-two');
    }

    public function createStepTwo()
    {
        $visitorId = session('visitor_id');
        if (!$visitorId) {
            return redirect()->route('check-in.step-one');
        }

        $visitor = Visitor::find($visitorId);
        $employees = Employee::with('user')->get();

        return view('frontend.check-in.step-two', compact('visitor', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'visitor_id' => 'required|exists:visitors,id',
            'purpose' => 'nullable|string',
            'host_person_id' => 'nullable|exists:people,id',
        ]);

        $visitor = Visitor::find($request->visitor_id);
        $employee = Employee::find($request->employee_id);

        // Generate QR code
        $qrCode = 'VIS-' . time() . '-' . rand(1000, 9999);

        // Create visiting detail
        $visitingDetail = VisitingDetails::create([
            'visitor_id' => $visitor->id,
            'employee_id' => $employee->id,
            'reg_no' => $qrCode,
            'checkin_time' => now(),
            'purpose' => $request->purpose,
            'status' => 'pending',
            'added_by' => auth()->id(),
        ]);

        // Create invitation if host_person_id is provided
        $invitationId = null;
        if ($request->host_person_id) {
            $invitation = Invitation::create([
                'facility_id' => $employee->user->facility_id ?? null,
                'host_person_id' => $request->host_person_id,
                'visitor_id' => $visitor->id,
                'visitor_email' => $visitor->email,
                'visitor_phone' => $visitor->phone,
                'qr_code' => $qrCode,
                'status' => 'pending',
                'expires_at' => now()->addDay(),
            ]);
            $invitationId = $invitation->id;
        }

        // ============================================
        // 🔔 SEND PUSH NOTIFICATIONS
        // ============================================
        try {
            $notificationService = new NotificationService();

            // 1. Notify the host (employee)
            $hostUserId = $employee->user_id;
            if ($hostUserId) {
                $notificationService->notifyHost(
                    $hostUserId,
                    $visitor->name,
                    $invitationId
                );
                Log::info("Push notification sent to host: {$hostUserId}");
            }

            // 2. Notify security guards at this facility
            $facilityId = $employee->user->facility_id ?? null;
            if ($facilityId) {
                $notificationService->notifySecurity(
                    $facilityId,
                    $visitor->name,
                    'pending'
                );
                Log::info("Push notification sent to security for facility: {$facilityId}");
            }

        } catch (\Exception $e) {
            Log::error('Failed to send push notifications: ' . $e->getMessage());
        }

        // Clear session
        session()->forget('visitor_id');

        return redirect()->route('check-in.show', $visitingDetail->id)
            ->with('success', 'Visitor checked in successfully!');
    }

    public function show($id)
    {
        $visitingDetail = VisitingDetails::with(['visitor', 'employee.user'])->findOrFail($id);
        return view('frontend.check-in.show', compact('visitingDetail'));
    }

    public function visitor_return(Request $request)
    {
        return view('frontend.check-in.return');
    }

    public function find_visitor(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $visitor = Visitor::where('phone', $request->phone)->first();

        if ($visitor) {
            return redirect()->route('check-in.step-two')->with('visitor_id', $visitor->id);
        }

        return redirect()->back()->with('error', 'Visitor not found. Please register them.');
    }

    public function pre_registered()
    {
        return view('frontend.check-in.pre-registered');
    }

    public function find_pre_visitor(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $preRegister = PreRegister::where('reference', $request->reference)
            ->orWhere('phone', $request->reference)
            ->first();

        if ($preRegister) {
            // Create visitor from pre-registration
            $visitor = Visitor::where('phone', $preRegister->phone)->first();
            if (!$visitor) {
                $visitor = Visitor::create([
                    'name' => $preRegister->name,
                    'phone' => $preRegister->phone,
                    'email' => $preRegister->email,
                    'reg_no' => 'VIS-' . time(),
                ]);
            }

            session(['visitor_id' => $visitor->id]);
            session(['pre_register_id' => $preRegister->id]);

            return redirect()->route('check-in.step-two')->with('success', 'Pre-registered visitor found!');
        }

        return redirect()->back()->with('error', 'Pre-registration not found.');
    }

    public function visitorDetails($visitorPhone)
    {
        // For QR code scanning
        $visitor = Visitor::where('phone', $visitorPhone)->first();
        if ($visitor) {
            return response()->json($visitor);
        }
        return response()->json(['error' => 'Visitor not found'], 404);
    }

    public function preVisitorDetails($visitorPhone)
    {
        // For QR code scanning - pre-registered visitors
        $preRegister = PreRegister::where('phone', $visitorPhone)->first();
        if ($preRegister) {
            return response()->json($preRegister);
        }
        return response()->json(['error' => 'Pre-registered visitor not found'], 404);
    }
}