<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'created_by',
        'title',
        'message',
        'severity',
        'status',
        'target_audience',
        'sent_at',
        'expires_at',
        'total_recipients',
        'total_acknowledged',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->hasMany(AlertRecipient::class);
    }

    public function acknowledgments()
    {
        return $this->hasMany(AlertAcknowledgment::class);
    }

    public function tokens()
    {
        return $this->hasMany(AlertAcknowledgmentToken::class);
    }

    public function getSeverityColorAttribute()
    {
        return match ($this->severity) {
            'warning' => 'warning',
            'critical' => 'orange',
            'emergency' => 'danger',
            default => 'secondary',
        };
    }

    public function getSeverityIconAttribute()
    {
        return match ($this->severity) {
            'warning' => 'fas fa-exclamation-triangle',
            'critical' => 'fas fa-exclamation-circle',
            'emergency' => 'fas fa-bell',
            default => 'fas fa-info-circle',
        };
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'sent')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}