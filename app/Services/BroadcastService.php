<?php

namespace App\Services;

use App\Models\Broadcast;
use App\Models\BroadcastRecipient;
use App\Models\BroadcastTemplate;
use App\Models\User;
use App\Models\Person;
use App\Models\Visitor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class BroadcastService
{
    /**
     * Send a broadcast message.
     */
    public function sendBroadcast($facilityId, $createdBy, $data)
    {
        DB::beginTransaction();

        try {
            // Create the broadcast
            $broadcast = Broadcast::create([
                'facility_id' => $facilityId,
                'created_by' => $createdBy,
                'title' => $data['title'],
                'message' => $data['message'],
                'target_groups' => $data['target_groups'],
                'channel' => $data['channel'] ?? 'whatsapp',
                'status' => $data['scheduled_at'] ? 'scheduled' : 'sent',
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'sent_at' => $data['scheduled_at'] ? null : now(),
            ]);

            // Get recipients
            $recipients = $this->getRecipients($facilityId, $data['target_groups']);

            // Send to each recipient
            foreach ($recipients as $recipient) {
                $this->sendToRecipient($broadcast, $recipient);
            }

            // Update counts
            $broadcast->total_recipients = $broadcast->recipients()->count();
            $broadcast->total_delivered = $broadcast->recipients()->where('status', 'delivered')->count();
            $broadcast->total_failed = $broadcast->recipients()->where('status', 'failed')->count();
            $broadcast->save();

            DB::commit();

            return $broadcast;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Broadcast failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get recipients based on target groups.
     */
    public function getRecipients($facilityId, $groups)
    {
        $recipients = [];

        if (in_array('residents', $groups)) {
            $residents = Person::where('facility_id', $facilityId)
                ->whereHas('residentProfile')
                ->get();
            foreach ($residents as $resident) {
                $recipients[] = [
                    'type' => 'resident',
                    'model' => $resident,
                    'phone' => $resident->phone,
                    'email' => $resident->email,
                ];
            }
        }

        if (in_array('employees', $groups)) {
            $employees = User::where('facility_id', $facilityId)
                ->whereHas('employee')
                ->get();
            foreach ($employees as $employee) {
                $recipients[] = [
                    'type' => 'employee',
                    'model' => $employee,
                    'phone' => $employee->phone,
                    'email' => $employee->email,
                ];
            }
        }

        if (in_array('security', $groups)) {
            $security = User::where('facility_id', $facilityId)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Security');
                })
                ->get();
            foreach ($security as $user) {
                $recipients[] = [
                    'type' => 'security',
                    'model' => $user,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ];
            }
        }

        if (in_array('visitors', $groups)) {
            $visitors = Visitor::where('facility_id', $facilityId)
                ->whereHas('visitingDetails', function ($query) {
                    $query->whereNull('checkout_time');
                })
                ->get();
            foreach ($visitors as $visitor) {
                $recipients[] = [
                    'type' => 'visitor',
                    'model' => $visitor,
                    'phone' => $visitor->phone,
                    'email' => $visitor->email,
                ];
            }
        }

        return $recipients;
    }

    /**
     * Send to a single recipient.
     */
    public function sendToRecipient($broadcast, $recipientData)
    {
        $model = $recipientData['model'];
        $phone = $recipientData['phone'];

        // Create recipient record
        $recipient = BroadcastRecipient::create([
            'broadcast_id' => $broadcast->id,
            'recipient_type' => get_class($model),
            'recipient_id' => $model->id,
            'phone' => $phone,
            'email' => $recipientData['email'] ?? null,
            'channel' => $broadcast->channel === 'both' ? 'whatsapp' : $broadcast->channel,
            'status' => 'pending',
        ]);

        // Send WhatsApp message
        if ($broadcast->channel === 'whatsapp' || $broadcast->channel === 'both') {
            $this->sendWhatsAppMessage($broadcast, $recipient, $phone);
        }

        // Send SMS if selected
        if ($broadcast->channel === 'sms' || $broadcast->channel === 'both') {
            $this->sendSmsMessage($broadcast, $recipient, $phone);
        }

        return $recipient;
    }

    /**
     * Send WhatsApp message via Twilio.
     */
    public function sendWhatsAppMessage($broadcast, $recipient, $phone)
    {
        try {
            $message = $broadcast->message;

            // Add broadcast title
            $fullMessage = "*{$broadcast->title}*\n\n{$message}";

            // Send via Twilio
            $response = Http::withBasicAuth(
                env('TWILIO_ACCOUNT_SID'),
                env('TWILIO_AUTH_TOKEN')
            )->post("https://api.twilio.com/2010-04-01/Accounts/" . env('TWILIO_ACCOUNT_SID') . "/Messages.json", [
                'From' => env('TWILIO_FROM'),
                'To' => 'whatsapp:' . $phone,
                'Body' => $fullMessage,
            ]);

            if ($response->successful()) {
                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                return true;
            } else {
                $recipient->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);
                return false;
            }

        } catch (\Exception $e) {
            $recipient->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error('WhatsApp broadcast failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS message via Twilio.
     */
    public function sendSmsMessage($broadcast, $recipient, $phone)
    {
        try {
            $message = "*{$broadcast->title}*\n\n{$broadcast->message}";

            $response = Http::withBasicAuth(
                env('TWILIO_ACCOUNT_SID'),
                env('TWILIO_AUTH_TOKEN')
            )->post("https://api.twilio.com/2010-04-01/Accounts/" . env('TWILIO_ACCOUNT_SID') . "/Messages.json", [
                'From' => env('TWILIO_PHONE_NUMBER'),
                'To' => $phone,
                'Body' => $message,
            ]);

            if ($response->successful()) {
                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                return true;
            } else {
                return false;
            }

        } catch (\Exception $e) {
            Log::error('SMS broadcast failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get broadcast statistics.
     */
    public function getStats($facilityId)
    {
        $broadcasts = Broadcast::where('facility_id', $facilityId);

        return [
            'total' => $broadcasts->count(),
            'sent' => $broadcasts->where('status', 'sent')->count(),
            'scheduled' => $broadcasts->where('status', 'scheduled')->count(),
            'failed' => $broadcasts->where('status', 'failed')->count(),
            'draft' => $broadcasts->where('status', 'draft')->count(),
        ];
    }
}