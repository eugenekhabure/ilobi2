<?php

namespace App\Http\Controllers;

use App\Models\AccessDevice;
use App\Models\AccessOTP;
use App\Models\AccessLog;
use App\Services\HikvisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HikvisionController extends Controller
{
    protected $hikvisionService;

    public function __construct(HikvisionService $hikvisionService)
    {
        $this->hikvisionService = $hikvisionService;
        $this->middleware('auth')->except(['webhook', 'verifyOtp']);
    }

    /**
     * Display a listing of Hikvision devices.
     */
    public function index(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $devices = AccessDevice::where('facility_id', $facilityId)
            ->where('brand', 'hikvision')
            ->orderBy('name')
            ->paginate(20);

        $stats = [
            'total' => AccessDevice::where('facility_id', $facilityId)->where('brand', 'hikvision')->count(),
            'online' => AccessDevice::where('facility_id', $facilityId)->where('brand', 'hikvision')->where('status', 'online')->count(),
            'offline' => AccessDevice::where('facility_id', $facilityId)->where('brand', 'hikvision')->where('status', 'offline')->count(),
            'error' => AccessDevice::where('facility_id', $facilityId)->where('brand', 'hikvision')->where('status', 'error')->count(),
        ];

        return view('admin.hikvision.index', compact('devices', 'stats'));
    }

    /**
     * Show the form for creating a new Hikvision device.
     */
    public function create()
    {
        $facilities = \App\Models\Facility::where('is_active', true)->get();
        return view('admin.hikvision.create', compact('facilities'));
    }

    /**
     * Store a newly created Hikvision device.
     */
    public function store(Request $request)
    {
        $facilityId = Auth::user()->facility_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'device_ip' => 'required|ip',
            'device_port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'door_number' => 'nullable|integer|min:1',
        ]);

        $device = AccessDevice::create([
            'facility_id' => $facilityId,
            'name' => $request->name,
            'brand' => 'hikvision',
            'device_ip' => $request->device_ip,
            'device_port' => $request->device_port ?? 80,
            'username' => $request->username,
            'password' => $request->password,
            'door_number' => $request->door_number ?? 1,
            'status' => 'offline',
            'is_active' => true,
        ]);

        return redirect()->route('admin.hikvision.index')
            ->with('success', 'Hikvision device added successfully!');
    }

    /**
     * Show the form for editing a Hikvision device.
     */
    public function edit($id)
    {
        $facilityId = Auth::user()->facility_id;

        $device = AccessDevice::where('facility_id', $facilityId)
            ->where('brand', 'hikvision')
            ->findOrFail($id);

        $facilities = \App\Models\Facility::where('is_active', true)->get();

        return view('admin.hikvision.edit', compact('device', 'facilities'));
    }

    /**
     * Update a Hikvision device.
     */
    public function update(Request $request, $id)
    {
        $facilityId = Auth::user()->facility_id;

        $device = AccessDevice::where('facility_id', $facilityId)
            ->where('brand', 'hikvision')
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'device_ip' => 'required|ip',
            'device_port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'door_number' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $device->update([
            'name' => $request->name,
            'device_ip' => $request->device_ip,
            'device_port' => $request->device_port ?? 80,
            'username' => $request->username,
            'password' => $request->password,
            'door_number' => $request->door_number ?? 1,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.hikvision.index')
            ->with('success', 'Hikvision device updated successfully!');
    }

    /**
     * Delete a Hikvision device.
     */
    public function destroy($id)
    {
        $facilityId = Auth::user()->facility_id;

        $device = AccessDevice::where('facility_id', $facilityId)
            ->where('brand', 'hikvision')
            ->findOrFail($id);

        $device->delete();

        return redirect()->route('admin.hikvision.index')
            ->with('success', 'Hikvision device deleted successfully!');
    }

    /**
     * Test connection to a Hikvision device.
     */
    public function testConnection($id)
    {
        $facilityId = Auth::user()->facility_id;

        $device = AccessDevice::where('facility_id', $facilityId)
            ->where('brand', 'hikvision')
            ->findOrFail($id);

        $result = $this->hikvisionService->testConnection($device);

        return response()->json($result);
    }

    /**
     * Unlock a door via Hikvision.
     */
    public function unlockDoor($id)
    {
        $facilityId = Auth::user()->facility_id;

        $device = AccessDevice::where('facility_id', $facilityId)
            ->where('brand', 'hikvision')
            ->findOrFail($id);

        $result = $this->hikvisionService->unlockDoor($device->door_number ?? 1);

        // Log the action
        AccessLog::create([
            'facility_id' => $facilityId,
            'loggable_type' => 'App\Models\AccessDevice',
            'loggable_id' => $device->id,
            'action' => 'door_unlock',
            'performed_by' => Auth::id(),
            'details' => [
                'device' => $device->name,
                'door' => $device->door_number,
                'result' => $result,
            ],
        ]);

        if ($result['success']) {
            return redirect()->route('admin.hikvision.index')
                ->with('success', 'Door unlocked successfully!');
        }

        return redirect()->route('admin.hikvision.index')
                ->with('error', 'Failed to unlock door: ' . $result['message']);
    }

    /**
     * Webhook endpoint for Hikvision OTP validation.
     * This is called by the Hikvision device when a guest enters an OTP.
     */
    public function webhook(Request $request)
    {
        Log::info('Hikvision webhook received', $request->all());

        $request->validate([
            'otp_code' => 'required|string|max:10',
            'device_id' => 'required|string',
            'door_number' => 'nullable|integer',
        ]);

        // Find the device by device_id or IP
        $device = AccessDevice::where('device_ip', $request->device_id)
            ->orWhere('id', $request->device_id)
            ->first();

        if (!$device) {
            Log::error('Hikvision device not found: ' . $request->device_id);
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        // Validate OTP
        $otp = AccessOTP::where('facility_id', $device->facility_id)
            ->where('otp_code', $request->otp_code)
            ->where('status', 'active')
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 401);
        }

        if ($otp->isExpired()) {
            $otp->markExpired();
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired',
            ], 410);
        }

        // Mark OTP as used
        $otp->markUsed();

        // Unlock the door
        $unlockResult = $this->hikvisionService->unlockDoor($device->door_number ?? 1);

        // Log the access
        AccessLog::create([
            'facility_id' => $device->facility_id,
            'loggable_type' => 'App\Models\AccessOTP',
            'loggable_id' => $otp->id,
            'action' => 'otp_entry',
            'performed_by' => null,
            'details' => [
                'device' => $device->name,
                'door' => $device->door_number,
                'otp_code' => $otp->otp_code,
                'unlock_success' => $unlockResult['success'],
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP validated successfully',
            'door_unlocked' => $unlockResult['success'],
            'person_name' => $otp->person->full_name ?? 'Unknown',
        ]);
    }

    /**
     * Verify OTP via Hikvision (alternative endpoint).
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|max:10',
            'facility_id' => 'required|exists:facilities,id',
        ]);

        $otp = AccessOTP::where('facility_id', $request->facility_id)
            ->where('otp_code', $request->otp_code)
            ->where('status', 'active')
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 401);
        }

        if ($otp->isExpired()) {
            $otp->markExpired();
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired',
            ], 410);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP is valid',
            'expires_at' => $otp->expires_at,
            'person_name' => $otp->person->full_name ?? 'Unknown',
        ]);
    }
}