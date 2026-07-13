<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'name',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'department_id');
    }

    public function getIconHtmlAttribute()
    {
        $icons = [
            'security' => '🛡️',
            'management' => '👔',
            'maintenance' => '🔧',
            'cleaning' => '🧹',
            'reception' => '📋',
            'gardening' => '🌳',
            'other' => '👤',
        ];
        return $icons[$this->icon] ?? '👤';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}