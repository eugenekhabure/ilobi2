<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'created_by',
        'title',
        'message',
        'target_groups',
        'channel',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'total_delivered',
        'total_failed',
    ];

    protected $casts = [
        'target_groups' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
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
        return $this->hasMany(BroadcastRecipient::class);
    }

    public function getTargetGroupsLabelAttribute()
    {
        if (!$this->target_groups) {
            return 'None';
        }
        $labels = [
            'residents' => 'Residents',
            'employees' => 'Employees',
            'security' => 'Security Team',
            'visitors' => 'Visitors',
        ];
        return collect($this->target_groups)->map(fn($g) => $labels[$g] ?? $g)->implode(', ');
    }

    public function getChannelIconAttribute()
    {
        return match ($this->channel) {
            'whatsapp' => 'fab fa-whatsapp',
            'sms' => 'fas fa-sms',
            'both' => 'fas fa-phone-alt',
            default => 'fas fa-message',
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'sent' => 'success',
            'scheduled' => 'warning',
            'failed' => 'danger',
            default => 'secondary',
        };
    }
}