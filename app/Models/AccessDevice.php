<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'name',
        'brand',
        'device_ip',
        'device_port',
        'username',
        'password',
        'api_key',
        'door_number',
        'status',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function isOnline()
    {
        return $this->status === 'online';
    }

    public function markOnline()
    {
        $this->update(['status' => 'online']);
    }

    public function markOffline()
    {
        $this->update(['status' => 'offline']);
    }

    public function markError()
    {
        $this->update(['status' => 'error']);
    }

    public function getFullAddressAttribute()
    {
        return $this->device_ip . ':' . $this->device_port;
    }
}