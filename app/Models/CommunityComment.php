<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'author_id',
        'parent_id',
        'content',
        'media',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function author()
    {
        return $this->belongsTo(Person::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(CommunityComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(CommunityComment::class, 'parent_id');
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}