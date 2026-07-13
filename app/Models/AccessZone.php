<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'name', 'description'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }
}