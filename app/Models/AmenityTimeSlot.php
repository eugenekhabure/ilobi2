<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'amenity_id',
        'day_of_week',
        'start_time',
        'end_time',
        'duration_minutes',
        'max_bookings',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }

    public function getDayLabelAttribute()
    {
        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
        return $days[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}