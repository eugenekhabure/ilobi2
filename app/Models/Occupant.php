<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupant extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'sub_unit_id', 'name', 'contact_phone', 'contact_email'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function employeeProfiles()
    {
        return $this->hasMany(EmployeeProfile::class);
    }
}