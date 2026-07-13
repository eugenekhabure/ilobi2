<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'module_key', 'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Check if a specific module is enabled for a facility
     */
    public static function isEnabled($facilityId, $moduleKey)
    {
        return self::where('facility_id', $facilityId)
            ->where('module_key', $moduleKey)
            ->where('is_enabled', true)
            ->exists();
    }
}