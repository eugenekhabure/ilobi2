<?php

namespace App\Http\Controllers;

use App\Models\AccessDevice;
use App\Models\Facility;
use Illuminate\Http\Request;

class AccessDeviceController extends Controller
{
    /**
     * Display a listing of access devices.
     */
    public function index(Request $request)
    {
        $query = AccessDevice::query();

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }
        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $devices = $query->with('facility')->get();

        if ($request->wantsJson()) {
            return response()->json($devices);
        }

        return view('admin.access-devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new access device.
     */
    public function create()
    {
        $facilities = Facility::all();
        return view('admin.access-devices.create', compact('facilities'));
    }

    /**
     * Store a newly created access device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'name' => 'required|string|max:255',
            'brand' => 'required|in:zkteco,hikvision,generic',
            'device_ip' => 'nullable|ip',
            'device_port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'api_key' => 'nullable|string',
            'door_number' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $device = AccessDevice::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Device created successfully',
                'device' => $device,
            ], 201);
        }

        return redirect()->route('admin.access-devices.index')
            ->with('success', 'Access device created successfully!');
    }

    /**
     * Display the specified access device.
     */
    public function show(AccessDevice $accessDevice)
    {
        return response()->json($accessDevice->load('facility'));
    }

    /**
     * Show the form for editing the specified access device.
     */
    public function edit(AccessDevice $accessDevice)
    {
        $facilities = Facility::all();
        return view('admin.access-devices.edit', compact('accessDevice', 'facilities'));
    }

    /**
     * Update the specified access device in storage.
     */
    public function update(Request $request, AccessDevice $accessDevice)
    {
        $validated = $request->validate([
            'facility_id' => 'sometimes|exists:facilities,id',
            'name' => 'sometimes|string|max:255',
            'brand' => 'sometimes|in:zkteco,hikvision,generic',
            'device_ip' => 'nullable|ip',
            'device_port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'api_key' => 'nullable|string',
            'door_number' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $accessDevice->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Device updated successfully',
                'device' => $accessDevice,
            ]);
        }

        return redirect()->route('admin.access-devices.index')
            ->with('success', 'Access device updated successfully!');
    }

    /**
     * Remove the specified access device from storage.
     */
    public function destroy(AccessDevice $accessDevice)
    {
        $accessDevice->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Device deleted successfully']);
        }

        return redirect()->route('admin.access-devices.index')
            ->with('success', 'Access device deleted successfully!');
    }

    /**
     * Test connection to the access device.
     */
    public function testConnection(AccessDevice $accessDevice)
    {
        // For now, return a mock response
        // In production, this would ping the actual device
        return response()->json([
            'device' => $accessDevice,
            'status' => 'online',
            'message' => 'Connection test successful',
        ]);
    }
}