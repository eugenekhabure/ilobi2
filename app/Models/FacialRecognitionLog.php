<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacialRecognitionLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'related_id',
        'full_name',
        'phone_number',
        'image_path',
        'confidence_score',
        'status',
        'face_data',
        'device_name',
        'ip_address',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'face_data' => 'array',
        'confidence_score' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the related model (polymorphic).
     * This could be Employee, Visitor, or Resident.
     */
    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Get the related employee if type is employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'related_id')->where('type', 'employee');
    }

    /**
     * Get the related visitor if type is visitor.
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'related_id')->where('type', 'visitor');
    }

    /**
     * Get the related resident if type is resident.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(ResidentProfile::class, 'related_id')->where('type', 'resident');
    }

    /**
     * Check if the face was matched.
     */
    public function isMatched(): bool
    {
        return $this->status === 'matched';
    }

    /**
     * Check if the face was unmatched (unknown person).
     */
    public function isUnmatched(): bool
    {
        return $this->status === 'unmatched';
    }

    /**
     * Check if there was an error during recognition.
     */
    public function hasError(): bool
    {
        return $this->status === 'error';
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'matched' => '✅ Matched',
            'unmatched' => '❌ Unmatched',
            'error' => '⚠️ Error',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get type label for display.
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Get confidence score as percentage.
     */
    public function getConfidencePercentageAttribute(): string
    {
        if ($this->confidence_score === null) {
            return 'N/A';
        }
        return number_format($this->confidence_score, 2) . '%';
    }

    /**
     * Get confidence color based on score.
     */
    public function getConfidenceColorAttribute(): string
    {
        if ($this->confidence_score === null) {
            return 'secondary';
        }
        if ($this->confidence_score >= 80) {
            return 'success';
        }
        if ($this->confidence_score >= 60) {
            return 'warning';
        }
        return 'danger';
    }

    /**
     * Scope a query to only include matched logs.
     */
    public function scopeMatched($query)
    {
        return $query->where('status', 'matched');
    }

    /**
     * Scope a query to only include unmatched logs.
     */
    public function scopeUnmatched($query)
    {
        return $query->where('status', 'unmatched');
    }

    /**
     * Scope a query to only include logs of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include logs with high confidence (>= 80%).
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 80);
    }

    /**
     * Scope a query to only include logs from a specific device.
     */
    public function scopeFromDevice($query, $deviceName)
    {
        return $query->where('device_name', $deviceName);
    }

    /**
     * Scope a query to only include today's logs.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->today());
    }
}