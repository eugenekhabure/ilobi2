<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Visit Approved: ' . $this->data['facility_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visitor.approval',
            with: [
                'visitorName' => $this->data['visitor_name'],
                'hostName' => $this->data['host_name'],
                'facilityName' => $this->data['facility_name'],
                'visitDate' => $this->data['visit_date'],
                'visitTime' => $this->data['visit_time'],
                'purpose' => $this->data['purpose'] ?? null,
                'checkinUrl' => $this->data['checkin_url'],
            ]
        );
    }
}