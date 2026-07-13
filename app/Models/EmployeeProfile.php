<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id', 'employee_code', 'occupant_id', 
        'department_id', 'job_title', 'hire_date'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function occupant()
    {
        return $this->belongsTo(Occupant::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}