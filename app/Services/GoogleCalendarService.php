<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;
    protected $service;
    protected $accessToken;

    public function __construct($accessToken = null)
    {
        $this->client = new Client();
        $this->client->setClientId(config('google-calendar.client_id'));
        $this->client->setClientSecret(config('google-calendar.client_secret'));
        $this->client->setRedirectUri(config('google-calendar.redirect_uri'));
        $this->client->setScopes(config('google-calendar.scopes'));
        $this->client->setAccessType(config('google-calendar.access_type'));
        $this->client->setApprovalPrompt(config('google-calendar.approval_prompt'));

        if ($accessToken) {
            $this->client->setAccessToken($accessToken);
        }

        $this->service = new Calendar($this->client);
    }

    /**
     * Get the Google Client instance.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set access token.
     */
    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
        return $this;
    }

    /**
     * Get the authentication URL.
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Authenticate with authorization code.
     */
    public function authenticate($code)
    {
        $this->client->authenticate($code);
        return $this->client->getAccessToken();
    }

    /**
     * Check if the token is expired and refresh if needed.
     */
    public function refreshTokenIfNeeded($token)
    {
        $this->client->setAccessToken($token);
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            return $this->client->getAccessToken();
        }
        return $token;
    }

    /**
     * Create a calendar event for a visitor booking.
     */
    public function createEvent($calendarId, $eventData)
    {
        try {
            // Create event object
            $event = new Event([
                'summary' => $eventData['summary'],
                'description' => $eventData['description'],
                'location' => $eventData['location'] ?? '',
                'start' => new EventDateTime([
                    'dateTime' => $eventData['start_time'],
                    'timeZone' => $eventData['timezone'] ?? 'Africa/Nairobi',
                ]),
                'end' => new EventDateTime([
                    'dateTime' => $eventData['end_time'],
                    'timeZone' => $eventData['timezone'] ?? 'Africa/Nairobi',
                ]),
                'attendees' => $eventData['attendees'] ?? [],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 60],
                        ['method' => 'popup', 'minutes' => 30],
                        ['method' => 'popup', 'minutes' => 10],
                    ],
                ],
                'colorId' => $eventData['color_id'] ?? '1',
            ]);

            // Add conference data if requested
            if ($eventData['conference'] ?? false) {
                $event->setConferenceData([
                    'createRequest' => [
                        'requestId' => uniqid(),
                        'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                    ],
                ]);
            }

            // Insert the event
            $event = $this->service->events->insert($calendarId, $event, [
                'conferenceDataVersion' => $eventData['conference'] ?? false ? 1 : 0,
                'sendUpdates' => 'all',
            ]);

            Log::info('Google Calendar event created: ' . $event->id);

            return [
                'success' => true,
                'event' => $event,
                'html_link' => $event->htmlLink,
            ];

        } catch (\Exception $e) {
            Log::error('Google Calendar event creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the user's primary calendar.
     */
    public function getPrimaryCalendar()
    {
        try {
            $calendarList = $this->service->calendarList->listCalendarList();
            foreach ($calendarList->getItems() as $calendar) {
                if ($calendar->getPrimary()) {
                    return $calendar;
                }
            }
            return $calendarList->getItems()[0] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to get primary calendar: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all calendars for the user.
     */
    public function getCalendars()
    {
        try {
            $calendarList = $this->service->calendarList->listCalendarList();
            return $calendarList->getItems();
        } catch (\Exception $e) {
            Log::error('Failed to get calendars: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update an existing calendar event.
     */
    public function updateEvent($calendarId, $eventId, $eventData)
    {
        try {
            $event = $this->service->events->get($calendarId, $eventId);

            if (isset($eventData['summary'])) {
                $event->setSummary($eventData['summary']);
            }
            if (isset($eventData['description'])) {
                $event->setDescription($eventData['description']);
            }
            if (isset($eventData['location'])) {
                $event->setLocation($eventData['location']);
            }
            if (isset($eventData['start_time'])) {
                $event->setStart(new EventDateTime([
                    'dateTime' => $eventData['start_time'],
                    'timeZone' => $eventData['timezone'] ?? 'Africa/Nairobi',
                ]));
            }
            if (isset($eventData['end_time'])) {
                $event->setEnd(new EventDateTime([
                    'dateTime' => $eventData['end_time'],
                    'timeZone' => $eventData['timezone'] ?? 'Africa/Nairobi',
                ]));
            }

            $updatedEvent = $this->service->events->update($calendarId, $eventId, $event, [
                'sendUpdates' => 'all',
            ]);

            return [
                'success' => true,
                'event' => $updatedEvent,
            ];

        } catch (\Exception $e) {
            Log::error('Google Calendar event update failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a calendar event.
     */
    public function deleteEvent($calendarId, $eventId)
    {
        try {
            $this->service->events->delete($calendarId, $eventId);
            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Google Calendar event deletion failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create event data from a pre-registration.
     */
    public static function createEventDataFromPreRegister($preRegister, $host)
    {
        $startDateTime = $preRegister->expected_date . 'T' . $preRegister->expected_time . ':00';
        $endDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime . ' +1 hour'));

        $eventData = [
            'summary' => '👤 Visitor Meeting: ' . $preRegister->visitor->name,
            'description' => "Visitor: " . $preRegister->visitor->name . "\n"
                . "Phone: " . $preRegister->visitor->phone . "\n"
                . "Email: " . $preRegister->visitor->email . "\n"
                . "Purpose: " . ($preRegister->purpose ?? 'Business visit') . "\n"
                . "Reference: " . ($preRegister->reference ?? 'N/A') . "\n"
                . "\nPlease prepare for this visitor.",
            'location' => $preRegister->facility->name ?? 'Main Office',
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'timezone' => 'Africa/Nairobi',
            'attendees' => [
                ['email' => $host->email],
                ['email' => $preRegister->visitor->email],
            ],
            'color_id' => '2',
            'conference' => true,
        ];

        return $eventData;
    }
}