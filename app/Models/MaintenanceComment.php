<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id',
        'user_id',
        'comment',
        'user_type',
        'photo',
    ];

    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserNameAttribute()
    {
        return $this->user->name ?? 'Unknown User';
    }
}