<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'department_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'bio',
        'photo',
        'status',
        'is_emergency_contact',
        'working_hours',
        'metadata',
    ];

    protected $casts = [
        'is_emergency_contact' => 'boolean',
        'working_hours' => 'array',
        'metadata' => 'array',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function department()
    {
        return $this->belongsTo(StaffDepartment::class, 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'on_leave' => 'warning',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency_contact', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}