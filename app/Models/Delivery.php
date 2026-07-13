<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'courier_name', 'tracking_number', 
        'recipient_person_id', 'sub_unit_id', 'status', 
        'notes', 'delivered_at'
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function recipient()
    {
        return $this->belongsTo(Person::class, 'recipient_person_id');
    }

    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class);
    }
}