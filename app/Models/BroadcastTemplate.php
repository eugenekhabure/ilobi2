<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'name',
        'subject',
        'message',
        'target_groups',
        'channel',
        'is_active',
    ];

    protected $casts = [
        'target_groups' => 'array',
        'is_active' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}