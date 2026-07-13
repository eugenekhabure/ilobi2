<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'plate_number', 'make', 'model', 'color', 'owner_type', 'owner_id'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function owner()
    {
        return $this->morphTo();
    }
}