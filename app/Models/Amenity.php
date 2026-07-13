<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'name',
        'icon',
        'description',
        'location',
        'capacity',
        'max_booking_days',
        'advance_notice_hours',
        'price',
        'requires_approval',
        'is_active',
        'operating_hours',
        'settings',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'settings' => 'array',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function timeSlots()
    {
        return $this->hasMany(AmenityTimeSlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(AmenityBooking::class);
    }

    public function getIconHtmlAttribute()
    {
        $icons = [
            'gym' => '🏋️',
            'pool' => '🏊',
            'meeting_room' => '🏢',
            'conference_room' => '📊',
            'clubhouse' => '🏠',
            'tennis_court' => '🎾',
            'basketball_court' => '🏀',
            'playground' => '🎠',
            'parking' => '🅿️',
            'garden' => '🌳',
            'sauna' => '🧖',
            'spa' => '💆',
            'other' => '📍',
        ];
        return $icons[$this->icon] ?? '📍';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isAvailable($date, $startTime, $endTime)
    {
        // Check if there's any booking that overlaps
        $overlap = $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        return !$overlap;
    }
}