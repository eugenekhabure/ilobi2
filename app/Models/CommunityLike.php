<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'person_id',
    ];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}