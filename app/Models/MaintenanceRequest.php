<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'category_id',
        'requested_by',
        'assigned_to',
        'title',
        'description',
        'unit_number',
        'block_name',
        'priority',
        'status',
        'photo',
        'requested_at',
        'assigned_at',
        'completed_at',
        'admin_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function category()
    {
        return $this->belongsTo(MaintenanceCategory::class);
    }

    public function requester()
    {
        return $this->belongsTo(Person::class, 'requested_by');
    }

    public function assignee()
    {
        return $this->belongsTo(Person::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(MaintenanceComment::class);
    }

    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'orange',
            'emergency' => 'danger',
            default => 'secondary',
        };
    }

    public function getPriorityLabelAttribute()
    {
        return ucfirst($this->priority);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'assigned' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeEmergency($query)
    {
        return $query->where('priority', 'emergency');
    }
}