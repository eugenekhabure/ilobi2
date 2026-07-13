<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'author_id',
        'title',
        'content',
        'type',
        'status',
        'featured_image',
        'is_featured',
        'published_at',
        'event_date',
        'location',
        'view_count',
        'like_count',
        'comment_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'event_date' => 'datetime',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function author()
    {
        return $this->belongsTo(Person::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(CommunityLike::class, 'post_id');
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'announcement' => '📢 Announcement',
            'event' => '🎉 Event',
            'classified' => '🏷️ Classified',
            'lost_found' => '🔍 Lost & Found',
            'general' => '📝 General',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'announcement' => 'primary',
            'event' => 'success',
            'classified' => 'warning',
            'lost_found' => 'danger',
            'general' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'published' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            'archived' => 'secondary',
            default => 'secondary',
        };
    }

    public function getExcerptAttribute()
    {
        return \Str::limit($this->content, 150);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function hasLiked($personId)
    {
        return $this->likes()->where('person_id', $personId)->exists();
    }

    public function incrementViews()
    {
        $this->increment('view_count');
    }
}