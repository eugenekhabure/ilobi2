<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'host_person_id', 'visitor_id', 
        'visitor_email', 'visitor_phone', 'sub_unit_id', 
        'qr_code', 'status', 'expires_at', 
        'checked_in_at', 'checked_out_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function host()
    {
        return $this->belongsTo(Person::class, 'host_person_id');
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class);
    }
}