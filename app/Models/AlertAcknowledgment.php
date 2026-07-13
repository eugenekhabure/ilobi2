<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertAcknowledgment extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_alert_id',
        'alert_recipient_id',
        'acknowledged_by_type',
        'acknowledged_by_id',
        'acknowledged_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function alert()
    {
        return $this->belongsTo(EmergencyAlert::class, 'emergency_alert_id');
    }

    public function recipient()
    {
        return $this->belongsTo(AlertRecipient::class);
    }

    public function acknowledgedBy()
    {
        return $this->morphTo();
    }
}