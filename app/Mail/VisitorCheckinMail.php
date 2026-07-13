<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorCheckinMail extends Mailable
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
            subject: '🚪 Visitor Check-in: ' . $this->data['visitor_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visitor.checkin',
            with: [
                'hostName' => $this->data['host_name'],
                'visitorName' => $this->data['visitor_name'],
                'visitorPhone' => $this->data['visitor_phone'] ?? null,
                'visitorEmail' => $this->data['visitor_email'] ?? null,
                'purpose' => $this->data['purpose'] ?? null,
                'checkinTime' => $this->data['checkin_time'],
                'facilityName' => $this->data['facility_name'],
                'approveUrl' => $this->data['approve_url'],
                'rejectUrl' => $this->data['reject_url'],
            ]
        );
    }
}