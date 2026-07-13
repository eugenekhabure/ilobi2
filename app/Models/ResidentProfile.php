<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id', 'sub_unit_id', 'lease_start', 'lease_end', 'is_owner'
    ];

    protected $casts = [
        'is_owner' => 'boolean',
        'lease_start' => 'date',
        'lease_end' => 'date',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class);
    }
}