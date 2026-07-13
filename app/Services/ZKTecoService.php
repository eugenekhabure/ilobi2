<?php

namespace App\Services;

use App\Models\AccessDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZKTecoService
{
    protected $device;

    public function __construct(AccessDevice $device)
    {
        $this->device = $device;
    }

    /**
     * Unlock a door
     */
    public function unlockDoor($doorNumber = 1, $timeout = 5)
    {
        try {
            $url = "http://{$this->device->device_ip}:{$this->device->device_port}/access/control";

            $response = Http::timeout(5)->post($url, [
                'action' => 'unlock',
                'door' => $doorNumber,
                'timeout' => $timeout,
            ]);

            if ($response->successful()) {
                $this->device->markOnline();
                return ['success' => true, 'message' => 'Door unlocked'];
            }

            $this->device->markError();
            return ['success' => false, 'message' => 'Failed to unlock door'];

        } catch (\Exception $e) {
            $this->device->markOffline();
            Log::error('ZKTeco error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Device unreachable'];
        }
    }

    /**
     * Lock a door
     */
    public function lockDoor($doorNumber = 1)
    {
        try {
            $url = "http://{$this->device->device_ip}:{$this->device->device_port}/access/control";

            $response = Http::timeout(5)->post($url, [
                'action' => 'lock',
                'door' => $doorNumber,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Door locked'];
            }

            return ['success' => false, 'message' => 'Failed to lock door'];

        } catch (\Exception $e) {
            Log::error('ZKTeco error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Device unreachable'];
        }
    }

    /**
     * Get device status
     */
    public function getStatus()
    {
        try {
            $url = "http://{$this->device->device_ip}:{$this->device->device_port}/status";

            $response = Http::timeout(3)->get($url);

            if ($response->successful()) {
                $this->device->markOnline();
                return ['online' => true, 'data' => $response->json()];
            }

            $this->device->markError();
            return ['online' => false, 'data' => null];

        } catch (\Exception $e) {
            $this->device->markOffline();
            return ['online' => false, 'data' => null];
        }
    }

    /**
     * Validate OTP via ZKTeco webhook
     * This is called when a guest enters an OTP on the keypad
     */
    public function handleOTPValidation($otpCode, $doorNumber = 1)
    {
        // This will be called from the webhook endpoint
        // The actual validation will be done by AccessOTPController

        return [
            'success' => true,
            'otp_code' => $otpCode,
            'door' => $doorNumber,
        ];
    }

    /**
     * Test device connection
     */
    public function testConnection()
    {
        return $this->getStatus();
    }
}