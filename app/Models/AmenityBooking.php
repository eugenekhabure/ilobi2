<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'amenity_id',
        'booked_by',
        'booking_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'guests_count',
        'notes',
        'status',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }

    public function bookedBy()
    {
        return $this->belongsTo(Person::class, 'booked_by');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('booking_date', $date);
    }

    public function scopeForAmenity($query, $amenityId)
    {
        return $query->where('amenity_id', $amenityId);
    }
}