<?php

namespace App\Http\Controllers;

use App\Models\AccessOTP;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessOTPController extends Controller
{
    /**
     * Generate a new OTP for a guest
     */
    public function generate(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'invitation_id' => 'nullable|exists:invitations,id',
            'length' => 'nullable|integer|min:4|max:6',
        ]);

        $personId = Auth::user()->person->id ?? null;
        if (!$personId) {
            return response()->json(['error' => 'User has no person profile'], 422);
        }

        $otp = AccessOTP::generate(
            $request->facility_id,
            $personId,
            $request->length ?? 4
        );

        if ($request->invitation_id) {
            $otp->update(['invitation_id' => $request->invitation_id]);
        }

        return response()->json([
            'success' => true,
            'otp' => $otp,
            'expires_at' => $otp->expires_at,
        ]);
    }

    /**
     * Validate an OTP (called by the keypad/hardware)
     */
    public function validateOtp(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'otp_code' => 'required|string|max:10',
        ]);

        $otp = AccessOTP::where('facility_id', $request->facility_id)
            ->where('otp_code', $request->otp_code)
            ->where('status', 'active')
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 404);
        }

        if ($otp->isExpired()) {
            $otp->markExpired();
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired',
            ], 410);
        }

        if ($otp->isUsed()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has already been used',
            ], 422);
        }

        // Validate
        $otp->markUsed();

        // Log the access
        $otp->facility->accessLogs()->create([
            'loggable_type' => 'App\Models\Visitor',
            'loggable_id' => $otp->visitor_id,
            'action' => 'otp_entry',
            'performed_by' => Auth::id(),
            'details' => [
                'otp_id' => $otp->id,
                'otp_code' => $otp->otp_code,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP validated successfully',
            'person_name' => $otp->person->full_name ?? 'Unknown',
            'unit' => $otp->person->residentProfile->subUnit->name ?? null,
        ]);
    }

    /**
     * Check OTP validity without consuming it
     */
    public function check(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'otp_code' => 'required|string|max:10',
        ]);

        $otp = AccessOTP::where('facility_id', $request->facility_id)
            ->where('otp_code', $request->otp_code)
            ->where('status', 'active')
            ->first();

        if (!$otp) {
            return response()->json(['valid' => false, 'message' => 'Invalid OTP']);
        }

        if ($otp->isExpired()) {
            return response()->json(['valid' => false, 'message' => 'Expired']);
        }

        return response()->json([
            'valid' => true,
            'expires_at' => $otp->expires_at,
        ]);
    }

    /**
     * Get OTP history for a facility
     */
    public function history(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
        ]);

        $otps = AccessOTP::where('facility_id', $request->facility_id)
            ->with(['person', 'visitor'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($otps);
    }

    /**
     * Send OTP via WhatsApp (optional)
     */
    public function sendViaWhatsApp(Request $request)
    {
        $request->validate([
            'otp_id' => 'required|exists:access_otps,id',
            'phone' => 'required|string',
        ]);

        $otp = AccessOTP::find($request->otp_id);

        // Here you would integrate with Twilio WhatsApp
        // For now, just return success

        return response()->json([
            'success' => true,
            'message' => 'OTP sent via WhatsApp',
            'otp' => $otp->otp_code,
        ]);
    }
}