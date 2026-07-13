<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'logo', 
        'subscription_plan', 'subscription_expires_at'
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}