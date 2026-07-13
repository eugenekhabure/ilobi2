<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AlertAcknowledgmentToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_alert_id',
        'alert_recipient_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function alert()
    {
        return $this->belongsTo(EmergencyAlert::class, 'emergency_alert_id');
    }

    public function recipient()
    {
        return $this->belongsTo(AlertRecipient::class);
    }

    public static function generate($alertId, $recipientId)
    {
        return self::create([
            'emergency_alert_id' => $alertId,
            'alert_recipient_id' => $recipientId,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function isValid()
    {
        return !$this->used_at && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function markUsed()
    {
        $this->update([
            'used_at' => now(),
        ]);
    }
}