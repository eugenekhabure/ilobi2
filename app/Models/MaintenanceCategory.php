<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceCategory extends Model
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

    /**
     * Get the maintenance requests for this category.
     * Using 'category_id' as the foreign key.
     */
    public function requests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'category_id');
    }

    public function getIconHtmlAttribute()
    {
        $icons = [
            'plumbing' => 'fa-solid fa-wrench',
            'electrical' => 'fa-solid fa-bolt',
            'cleaning' => 'fa-solid fa-broom',
            'security' => 'fa-solid fa-shield',
            'hvac' => 'fa-solid fa-snowflake',
            'furniture' => 'fa-solid fa-chair',
            'pest_control' => 'fa-solid fa-bug',
            'other' => 'fa-solid fa-tools',
        ];
        return $icons[$this->icon] ?? 'fa-solid fa-tools';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}