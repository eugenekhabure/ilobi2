<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'parent_id', 'type', 'name', 'description'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function parent()
    {
        return $this->belongsTo(SubUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SubUnit::class, 'parent_id');
    }

    public function residentProfiles()
    {
        return $this->hasMany(ResidentProfile::class);
    }

    public function occupants()
    {
        return $this->hasMany(Occupant::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'destination_sub_unit_id');
    }
}