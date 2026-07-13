<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'first_name', 'last_name', 'email', 'phone', 'photo', 'notes'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function residentProfile()
    {
        return $this->hasOne(ResidentProfile::class);
    }

    public function vehicles()
    {
        return $this->morphMany(Vehicle::class, 'owner');
    }

    public function accessLogs()
    {
        return $this->morphMany(AccessLog::class, 'loggable');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'host_person_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'recipient_person_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}