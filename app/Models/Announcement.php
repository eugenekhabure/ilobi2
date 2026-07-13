<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'created_by',
        'title',
        'content',
        'category',
        'is_pinned',
        'expires_at',
        'published_at',
        'is_active',
        'view_count',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'general' => '📢 General',
            'security' => '🛡️ Security',
            'maintenance' => '🔧 Maintenance',
            'events' => '🎉 Events',
            'emergency' => '🚨 Emergency',
        ];
        return $labels[$this->category] ?? $this->category;
    }

    public function getCategoryColorAttribute()
    {
        return match ($this->category) {
            'general' => 'primary',
            'security' => 'warning',
            'maintenance' => 'info',
            'events' => 'success',
            'emergency' => 'danger',
            default => 'secondary',
        };
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPublished()
    {
        return $this->published_at && $this->published_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}