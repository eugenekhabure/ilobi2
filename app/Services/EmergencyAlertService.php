<?php

namespace App\Services;

use App\Models\EmergencyAlert;
use App\Models\AlertRecipient;
use App\Models\AlertAcknowledgmentToken;
use App\Models\User;
use App\Models\Person;
use App\Models\Visitor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class EmergencyAlertService
{
    /**
     * Send an emergency alert.
     */
    public function sendAlert($facilityId, $createdBy, $data)
    {
        DB::beginTransaction();

        try {
            // Create the alert
            $alert = EmergencyAlert::create([
                'facility_id' => $facilityId,
                'created_by' => $createdBy,
                'title' => $data['title'],
                'message' => $data['message'],
                'severity' => $data['severity'],
                'status' => 'sent',
                'target_audience' => $data['target_audience'],
                'sent_at' => now(),
                'expires_at' => isset($data['expires_at']) ? $data['expires_at'] : now()->addHours(24),
                'total_recipients' => 0,
                'total_acknowledged' => 0,
            ]);

            // Get recipients
            $recipients = $this->getRecipients($facilityId, $data['target_audience']);

            // Send to each recipient
            foreach ($recipients as $recipient) {
                $this->sendToRecipient($alert, $recipient);
            }

            // Update total recipients count
            $alert->total_recipients = $alert->recipients()->count();
            $alert->save();

            DB::commit();

            return $alert;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Emergency alert failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get recipients based on target audience.
     */
    public function getRecipients($facilityId, $audience)
    {
        $recipients = [];

        if (in_array('residents', $audience)) {
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

        if (in_array('employees', $audience)) {
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

        if (in_array('security', $audience)) {
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

        if (in_array('visitors', $audience)) {
            // Get currently checked-in visitors
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
     * Send alert to a single recipient.
     */
    public function sendToRecipient($alert, $recipientData)
    {
        $model = $recipientData['model'];
        $phone = $recipientData['phone'];
        $email = $recipientData['email'];

        // Create recipient record
        $recipient = AlertRecipient::create([
            'emergency_alert_id' => $alert->id,
            'recipient_type' => get_class($model),
            'recipient_id' => $model->id,
            'phone' => $phone,
            'email' => $email,
            'channel' => 'whatsapp',
            'status' => 'pending',
        ]);

        // Generate acknowledgment token
        $token = AlertAcknowledgmentToken::generate($alert->id, $recipient->id);

        // Send WhatsApp message
        $this->sendWhatsAppMessage($alert, $recipient, $phone, $token);

        // If WhatsApp fails, try SMS
        // $this->sendSmsMessage($alert, $recipient, $phone, $token);

        return $recipient;
    }

    /**
     * Send WhatsApp message via Twilio.
     */
    public function sendWhatsAppMessage($alert, $recipient, $phone, $token)
    {
        try {
            $message = $this->formatMessage($alert, $token->token);

            // Twilio WhatsApp API
            $response = Http::withBasicAuth(
                env('TWILIO_ACCOUNT_SID'),
                env('TWILIO_AUTH_TOKEN')
            )->post("https://api.twilio.com/2010-04-01/Accounts/" . env('TWILIO_ACCOUNT_SID') . "/Messages.json", [
                'From' => env('TWILIO_FROM'),
                'To' => 'whatsapp:' . $phone,
                'Body' => $message,
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
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format the alert message for WhatsApp.
     */
    public function formatMessage($alert, $token)
    {
        $severityEmoji = match ($alert->severity) {
            'warning' => '⚠️',
            'critical' => '🔴',
            'emergency' => '🚨',
            default => '📢',
        };

        $severityText = match ($alert->severity) {
            'warning' => 'WARNING',
            'critical' => 'CRITICAL',
            'emergency' => 'EMERGENCY',
            default => 'ALERT',
        };

        $acknowledgeUrl = url("/emergency/acknowledge/{$token}");

        return "{$severityEmoji} *{$severityText}*\n\n"
            . "*{$alert->title}*\n"
            . "{$alert->message}\n\n"
            . "📅 Sent: " . now()->format('d/m/Y H:i') . "\n"
            . "📍 Facility: {$alert->facility->name}\n\n"
            . "_Please acknowledge this alert:_\n"
            . "{$acknowledgeUrl}\n\n"
            . "🔒 ILOBI Security System";
    }

    /**
     * Acknowledge an alert.
     */
    public function acknowledgeAlert($token)
    {
        $tokenModel = AlertAcknowledgmentToken::where('token', $token)
            ->whereNull('used_at')
            ->first();

        if (!$tokenModel) {
            return ['success' => false, 'message' => 'Invalid or expired token'];
        }

        if (!$tokenModel->isValid()) {
            return ['success' => false, 'message' => 'Token has expired'];
        }

        DB::beginTransaction();

        try {
            $tokenModel->markUsed();

            $acknowledgment = AlertAcknowledgment::create([
                'emergency_alert_id' => $tokenModel->emergency_alert_id,
                'alert_recipient_id' => $tokenModel->alert_recipient_id,
                'acknowledged_by_type' => $tokenModel->recipient->recipient_type,
                'acknowledged_by_id' => $tokenModel->recipient->recipient_id,
                'acknowledged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Update the alert's total acknowledged count
            $alert = EmergencyAlert::find($tokenModel->emergency_alert_id);
            $alert->total_acknowledged = $alert->acknowledgments()->count();
            $alert->save();

            DB::commit();

            return ['success' => true, 'message' => 'Alert acknowledged successfully'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Acknowledgment failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to acknowledge'];
        }
    }

    /**
     * Get alert statistics.
     */
    public function getStats($facilityId)
    {
        $alerts = EmergencyAlert::where('facility_id', $facilityId);

        return [
            'total' => $alerts->count(),
            'sent' => $alerts->where('status', 'sent')->count(),
            'pending' => $alerts->where('status', 'draft')->count(),
            'expired' => $alerts->where('status', 'expired')->count(),
            'emergency' => $alerts->where('severity', 'emergency')->count(),
            'acknowledged' => $alerts->sum('total_acknowledged'),
        ];
    }
}