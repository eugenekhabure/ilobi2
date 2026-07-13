<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'visiting_detail_id',
        'visitor_id',
        'host_person_id',
        'rating',
        'comment',
        'host_rating',
        'security_rating',
        'cleanliness_rating',
        'overall_rating',
        'would_recommend',
        'response',
        'submitted_at',
        'ip_address',
        'user_agent',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'response' => 'array',
        'submitted_at' => 'datetime',
        'would_recommend' => 'boolean',
        'is_flagged' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function visitingDetail()
    {
        return $this->belongsTo(VisitingDetails::class, 'visiting_detail_id');
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function host()
    {
        return $this->belongsTo(Person::class, 'host_person_id');
    }

    public function getRatingLabelAttribute()
    {
        $labels = [
            1 => '🌟 Very Poor',
            2 => '🌟 Poor',
            3 => '🌟 Average',
            4 => '🌟 Good',
            5 => '🌟 Excellent',
        ];
        return $labels[$this->rating] ?? 'Not rated';
    }

    public function getRatingColorAttribute()
    {
        return match ($this->rating) {
            1 => 'danger',
            2 => 'danger',
            3 => 'warning',
            4 => 'info',
            5 => 'success',
            default => 'secondary',
        };
    }

    public function getStarsAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '⭐';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeLowRated($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }
}