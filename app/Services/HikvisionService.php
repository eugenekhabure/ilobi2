<?php

namespace App\Services;

use App\Models\AccessDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HikvisionService
{
    protected $device;

    public function __construct(AccessDevice $device)
    {
        $this->device = $device;
    }

    /**
     * Unlock a door using ISAPI protocol
     */
    public function unlockDoor($doorNumber = 1)
    {
        try {
            $url = "http://{$this->device->device_ip}/ISAPI/AccessControl/RemoteControl/door/{$doorNumber}";

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <RemoteControlDoor>
                        <cmd>open</cmd>
                    </RemoteControlDoor>';

            $response = Http::timeout(5)
                ->withBasicAuth($this->device->username, $this->device->password)
                ->withHeaders(['Content-Type' => 'application/xml'])
                ->put($url, $xml);

            if ($response->successful()) {
                $this->device->markOnline();
                return ['success' => true, 'message' => 'Door unlocked'];
            }

            $this->device->markError();
            return ['success' => false, 'message' => 'Failed to unlock door'];

        } catch (\Exception $e) {
            $this->device->markOffline();
            Log::error('Hikvision error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Device unreachable'];
        }
    }

    /**
     * Lock a door using ISAPI protocol
     */
    public function lockDoor($doorNumber = 1)
    {
        try {
            $url = "http://{$this->device->device_ip}/ISAPI/AccessControl/RemoteControl/door/{$doorNumber}";

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <RemoteControlDoor>
                        <cmd>close</cmd>
                    </RemoteControlDoor>';

            $response = Http::timeout(5)
                ->withBasicAuth($this->device->username, $this->device->password)
                ->withHeaders(['Content-Type' => 'application/xml'])
                ->put($url, $xml);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Door locked'];
            }

            return ['success' => false, 'message' => 'Failed to lock door'];

        } catch (\Exception $e) {
            Log::error('Hikvision error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Device unreachable'];
        }
    }

    /**
     * Get device status
     */
    public function getStatus()
    {
        try {
            $url = "http://{$this->device->device_ip}/ISAPI/System/status";

            $response = Http::timeout(3)
                ->withBasicAuth($this->device->username, $this->device->password)
                ->get($url);

            if ($response->successful()) {
                $this->device->markOnline();
                return ['online' => true, 'data' => $response->body()];
            }

            $this->device->markError();
            return ['online' => false, 'data' => null];

        } catch (\Exception $e) {
            $this->device->markOffline();
            return ['online' => false, 'data' => null];
        }
    }

    /**
     * Test device connection
     */
    public function testConnection()
    {
        return $this->getStatus();
    }
}