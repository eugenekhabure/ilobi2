<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyDigestMail extends Mailable
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
            subject: '📊 Daily Visitor Digest: ' . $this->data['date'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.digest.daily',
            with: [
                'date' => $this->data['date'],
                'facilityName' => $this->data['facility_name'],
                'totalVisitors' => $this->data['total_visitors'],
                'checkedIn' => $this->data['checked_in'],
                'checkedOut' => $this->data['checked_out'],
                'pending' => $this->data['pending'],
                'visitors' => $this->data['visitors'],
                'dashboardUrl' => $this->data['dashboard_url'],
            ]
        );
    }
}