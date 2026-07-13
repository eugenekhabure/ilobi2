<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'loggable_type', 'loggable_id', 
        'action', 'access_zone_id', 'performed_by', 'details'
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    public function accessZone()
    {
        return $this->belongsTo(AccessZone::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}