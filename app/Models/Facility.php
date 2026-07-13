<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'name', 'type', 'address', 
        'city', 'state', 'country', 'settings', 'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function subUnits()
    {
        return $this->hasMany(SubUnit::class);
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function occupants()
    {
        return $this->hasMany(Occupant::class);
    }

    public function accessZones()
    {
        return $this->hasMany(AccessZone::class);
    }

    public function modules()
    {
        return $this->hasMany(FacilityModule::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }
}