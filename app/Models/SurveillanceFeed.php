<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveillanceFeed extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'camera_url',
        'stream_url',
        'camera_type',
        'brand',
        'model',
        'ip_address',
        'port',
        'username',
        'password',
        'status',
        'is_recording',
        'recording_path',
        'storage_limit_days',
        'notes',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_recording' => 'boolean',
        'port' => 'integer',
        'storage_limit_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this surveillance feed.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if camera is online.
     */
    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    /**
     * Check if camera is recording.
     */
    public function isRecording(): bool
    {
        return $this->is_recording && $this->status === 'recording';
    }

    /**
     * Check if camera is offline.
     */
    public function isOffline(): bool
    {
        return $this->status === 'offline';
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'online' => '🟢 Online',
            'offline' => '🔴 Offline',
            'recording' => '🔵 Recording',
            'error' => '🟡 Error',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'online' => 'success',
            'offline' => 'danger',
            'recording' => 'primary',
            'error' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get camera type label for display.
     */
    public function getCameraTypeLabelAttribute(): string
    {
        return match ($this->camera_type) {
            'ip' => 'IP Camera',
            'usb' => 'USB Camera',
            'hikvision' => 'Hikvision',
            'zkteco' => 'ZK Teco',
            default => ucfirst($this->camera_type),
        };
    }

    /**
     * Get full camera URL with credentials (if available).
     */
    public function getFullCameraUrlAttribute(): string
    {
        if ($this->username && $this->password) {
            // Parse the URL and add credentials
            $url = parse_url($this->camera_url);
            return $url['scheme'] . '://' . $this->username . ':' . $this->password . '@' . $url['host'] . ($url['path'] ?? '');
        }
        return $this->camera_url;
    }

    /**
     * Get recording path display.
     */
    public function getRecordingPathDisplayAttribute(): string
    {
        return $this->recording_path ?? 'Not set';
    }

    /**
     * Scope a query to only include online cameras.
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    /**
     * Scope a query to only include offline cameras.
     */
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    /**
     * Scope a query to only include recording cameras.
     */
    public function scopeRecording($query)
    {
        return $query->where('status', 'recording')->where('is_recording', true);
    }

    /**
     * Scope a query to only include cameras of a specific type.
     */
    public function scopeOfType($query, $cameraType)
    {
        return $query->where('camera_type', $cameraType);
    }

    /**
     * Scope a query to only include cameras at a specific location.
     */
    public function scopeAtLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope a query to only include cameras with recording enabled.
     */
    public function scopeWithRecording($query)
    {
        return $query->where('is_recording', true);
    }
}