<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PreRegister;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the Google Calendar settings page.
     */
    public function settings()
    {
        $user = Auth::user();
        $isConnected = false;
        $calendars = [];
        $primaryCalendar = null;
        $authUrl = '';

        // Check if user has Google access token
        if ($user->google_access_token) {
            $isConnected = true;
            try {
                $calendarService = new GoogleCalendarService(json_decode($user->google_access_token, true));
                $calendars = $calendarService->getCalendars();
                $primaryCalendar = $calendarService->getPrimaryCalendar();
            } catch (\Exception $e) {
                $isConnected = false;
            }
        }

        // Always generate auth URL
        try {
            $calendarService = new GoogleCalendarService();
            $authUrl = $calendarService->getAuthUrl();
        } catch (\Exception $e) {
            $authUrl = '#';
        }

        // Get recent syncs (from pre_registers table)
        $recentSyncs = PreRegister::whereNotNull('google_event_id')
            ->with(['visitor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.settings.google-calendar', compact(
            'isConnected',
            'authUrl',
            'calendars',
            'primaryCalendar',
            'recentSyncs'
        ));
    }

    /**
     * Redirect to Google for authentication.
     */
    public function redirect()
    {
        $calendarService = new GoogleCalendarService();
        $authUrl = $calendarService->getAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return redirect()->route('admin.settings.google-calendar')
                ->with('error', 'Authorization failed. Please try again.');
        }

        try {
            $calendarService = new GoogleCalendarService();
            $accessToken = $calendarService->authenticate($code);

            // Save the access token to the user
            $user = Auth::user();
            $user->google_access_token = json_encode($accessToken);
            $user->google_refresh_token = $accessToken['refresh_token'] ?? null;
            if (isset($accessToken['expires_in'])) {
                $user->google_token_expires_at = now()->addSeconds($accessToken['expires_in']);
            }
            $user->save();

            return redirect()->route('admin.settings.google-calendar')
                ->with('success', 'Google Calendar connected successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.settings.google-calendar')
                ->with('error', 'Failed to connect Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Google Calendar.
     */
    public function disconnect(Request $request)
    {
        $user = Auth::user();
        $user->google_access_token = null;
        $user->google_refresh_token = null;
        $user->google_token_expires_at = null;
        $user->save();

        return redirect()->route('admin.settings.google-calendar')
            ->with('success', 'Google Calendar disconnected successfully!');
    }

    /**
     * Sync a pre-registration to Google Calendar.
     */
    public function syncPreRegister(Request $request)
    {
        $request->validate([
            'pre_register_id' => 'required|exists:pre_registers,id',
            'calendar_id' => 'nullable|string',
        ]);

        $user = Auth::user();

        if (!$user->google_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'Google Calendar not connected.',
            ], 401);
        }

        $preRegister = PreRegister::with(['visitor', 'facility', 'employee.user'])
            ->find($request->pre_register_id);

        if (!$preRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Pre-registration not found.',
            ], 404);
        }

        try {
            $calendarService = new GoogleCalendarService(json_decode($user->google_access_token, true));

            // Refresh token if expired
            $token = json_decode($user->google_access_token, true);
            $calendarService->setAccessToken($token);
            
            if ($calendarService->getClient()->isAccessTokenExpired()) {
                $calendarService->getClient()->fetchAccessTokenWithRefreshToken($token['refresh_token'] ?? '');
                $newToken = $calendarService->getClient()->getAccessToken();
                $user->google_access_token = json_encode($newToken);
                $user->save();
            }

            // Get the calendar ID
            $calendarId = $request->calendar_id ?: 'primary';

            // Create event data
            $hostName = $preRegister->employee->user->name ?? 'Host';
            $visitorName = $preRegister->visitor->name ?? 'Visitor';

            $startDateTime = $preRegister->expected_date . 'T' . $preRegister->expected_time . ':00';
            $endDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime . ' +1 hour'));

            $eventData = [
                'summary' => '👤 Visitor Meeting: ' . $visitorName,
                'description' => "Visitor: " . $visitorName . "\n"
                    . "Phone: " . ($preRegister->visitor->phone ?? 'N/A') . "\n"
                    . "Email: " . ($preRegister->visitor->email ?? 'N/A') . "\n"
                    . "Purpose: " . ($preRegister->purpose ?? 'Business visit') . "\n"
                    . "Reference: " . ($preRegister->reference ?? 'N/A') . "\n"
                    . "\nPlease prepare for this visitor.",
                'location' => $preRegister->facility->name ?? 'Main Office',
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'timezone' => 'Africa/Nairobi',
                'attendees' => [
                    ['email' => $preRegister->employee->user->email ?? ''],
                    ['email' => $preRegister->visitor->email ?? ''],
                ],
                'color_id' => '2',
                'conference' => true,
            ];

            // Create the event
            $result = $calendarService->createEvent($calendarId, $eventData);

            if ($result['success']) {
                // Save the event ID to the pre-registration
                $preRegister->google_event_id = $result['event']->id;
                $preRegister->google_event_link = $result['html_link'];
                $preRegister->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Calendar event created successfully!',
                    'event_link' => $result['html_link'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to create event',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the current user's calendar sync status.
     */
    public function status()
    {
        $user = Auth::user();
        $isConnected = false;
        $primaryCalendar = null;

        if ($user->google_access_token) {
            $isConnected = true;
            try {
                $calendarService = new GoogleCalendarService(json_decode($user->google_access_token, true));
                $primaryCalendar = $calendarService->getPrimaryCalendar();
            } catch (\Exception $e) {
                $isConnected = false;
            }
        }

        return response()->json([
            'is_connected' => $isConnected,
            'primary_calendar' => $primaryCalendar ? [
                'id' => $primaryCalendar->getId(),
                'summary' => $primaryCalendar->getSummary(),
            ] : null,
        ]);
    }
}