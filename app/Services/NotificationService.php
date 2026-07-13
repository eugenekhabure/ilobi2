<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorCheckinMail;
use App\Mail\VisitorApprovalMail;
use App\Mail\VisitorRejectionMail;
use App\Mail\PreRegisterConfirmationMail;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $subscriptions = PushSubscription::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        $sent = 0;
        foreach ($subscriptions as $subscription) {
            try {
                // Log the attempt (we'll implement actual sending later)
                Log::info("Sending push to user {$userId}: {$title}");
                $sent++;
            } catch (\Exception $e) {
                Log::error("Failed to send push: " . $e->getMessage());
            }
        }

        return $sent;
    }

    /**
     * Send a visitor check-in notification to the host.
     */
    public function notifyHost($hostId, $visitorName, $invitationId = null)
    {
        $title = '🚪 Visitor Arrived';
        $body = "{$visitorName} has arrived to see you!";
        $data = [
            'url' => '/pwa/visitors',
            'type' => 'visitor_checkin',
            'invitation_id' => $invitationId,
        ];

        return $this->sendToUser($hostId, $title, $body, $data);
    }

    /**
     * Send a visitor approval notification to security.
     */
    public function notifySecurity($facilityId, $visitorName, $status)
    {
        // Find security users for this facility
        $securityUsers = User::where('facility_id', $facilityId)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Security');
            })
            ->get();

        $sent = 0;
        foreach ($securityUsers as $user) {
            $title = $status === 'approved' ? '✅ Visitor Approved' : '❌ Visitor Rejected';
            $body = "{$visitorName} was {$status} by the host.";
            $data = [
                'url' => '/pwa/visitors',
                'type' => 'visitor_approval',
            ];
            $sent += $this->sendToUser($user->id, $title, $body, $data);
        }

        return $sent;
    }

    /**
     * Send a test notification.
     */
    public function sendTestNotification($userId)
    {
        return $this->sendToUser($userId, '🔔 Test Notification', 'Your push notifications are working!');
    }

    // ============================================
    // 📧 EMAIL NOTIFICATIONS
    // ============================================

    /**
     * Send email notification for visitor check-in.
     */
    public function sendVisitorCheckinEmail($hostEmail, $hostName, $data)
    {
        try {
            $emailData = [
                'host_name' => $hostName,
                'visitor_name' => $data['visitor_name'],
                'visitor_phone' => $data['visitor_phone'] ?? null,
                'visitor_email' => $data['visitor_email'] ?? null,
                'purpose' => $data['purpose'] ?? 'Business visit',
                'checkin_time' => $data['checkin_time'] ?? now()->format('d/m/Y H:i'),
                'facility_name' => $data['facility_name'],
                'approve_url' => $data['approve_url'] ?? '#',
                'reject_url' => $data['reject_url'] ?? '#',
            ];

            Mail::to($hostEmail)->send(new VisitorCheckinMail($emailData));
            Log::info("Visitor check-in email sent to: {$hostEmail}");
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send visitor check-in email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification for visitor approval.
     */
    public function sendVisitorApprovalEmail($visitorEmail, $data)
    {
        try {
            $emailData = [
                'visitor_name' => $data['visitor_name'],
                'host_name' => $data['host_name'],
                'facility_name' => $data['facility_name'],
                'visit_date' => $data['visit_date'] ?? now()->format('d/m/Y'),
                'visit_time' => $data['visit_time'] ?? now()->format('H:i'),
                'purpose' => $data['purpose'] ?? null,
                'checkin_url' => $data['checkin_url'] ?? '#',
            ];

            Mail::to($visitorEmail)->send(new VisitorApprovalMail($emailData));
            Log::info("Visitor approval email sent to: {$visitorEmail}");
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send visitor approval email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification for visitor rejection.
     */
    public function sendVisitorRejectionEmail($visitorEmail, $data)
    {
        try {
            $emailData = [
                'visitor_name' => $data['visitor_name'],
                'host_name' => $data['host_name'],
                'facility_name' => $data['facility_name'],
            ];

            Mail::to($visitorEmail)->send(new VisitorRejectionMail($emailData));
            Log::info("Visitor rejection email sent to: {$visitorEmail}");
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send visitor rejection email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification for pre-registration.
     */
    public function sendPreRegisterConfirmationEmail($visitorEmail, $data)
    {
        try {
            $emailData = [
                'visitor_name' => $data['visitor_name'],
                'host_name' => $data['host_name'],
                'facility_name' => $data['facility_name'],
                'visit_date' => $data['visit_date'] ?? now()->format('d/m/Y'),
                'visit_time' => $data['visit_time'] ?? now()->format('H:i'),
                'reference' => $data['reference'] ?? 'N/A',
                'checkin_url' => $data['checkin_url'] ?? '#',
            ];

            Mail::to($visitorEmail)->send(new PreRegisterConfirmationMail($emailData));
            Log::info("Pre-registration confirmation email sent to: {$visitorEmail}");
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send pre-registration confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email to multiple recipients (broadcast).
     */
    public function sendBroadcastEmail($recipients, $subject, $message, $data = [])
    {
        $sent = 0;
        foreach ($recipients as $recipient) {
            try {
                Mail::raw($message, function ($mail) use ($recipient, $subject) {
                    $mail->to($recipient['email'])
                        ->subject($subject);
                });
                $sent++;
            } catch (\Exception $e) {
                Log::error('Failed to send broadcast email to: ' . ($recipient['email'] ?? 'unknown') . ' - ' . $e->getMessage());
            }
        }
        return $sent;
    }

    /**
     * Get email notification preferences for a user.
     */
    public function getEmailPreferences($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'email_notifications' => true,
                'visitor_checkin_email' => true,
                'daily_digest_email' => false,
            ];
        }

        // You can store preferences in user settings or a separate table
        return [
            'email_notifications' => $user->email_notifications ?? true,
            'visitor_checkin_email' => $user->visitor_checkin_email ?? true,
            'daily_digest_email' => $user->daily_digest_email ?? false,
        ];
    }
}