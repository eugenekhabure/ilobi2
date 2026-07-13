<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_alert_id',
        'recipient_type',
        'recipient_id',
        'phone',
        'email',
        'channel',
        'status',
        'sent_at',
        'delivered_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function alert()
    {
        return $this->belongsTo(EmergencyAlert::class, 'emergency_alert_id');
    }

    public function recipient()
    {
        return $this->morphTo();
    }

    public function acknowledgment()
    {
        return $this->hasOne(AlertAcknowledgment::class);
    }

    public function token()
    {
        return $this->hasOne(AlertAcknowledgmentToken::class);
    }

    public function isAcknowledged()
    {
        return $this->acknowledgment()->exists();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}