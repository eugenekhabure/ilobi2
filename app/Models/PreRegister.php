<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Shipu\Watchable\Traits\HasAuditColumn;

class PreRegister extends Model
{
    use HasAuditColumn;

    protected $table = 'pre_registers';
    protected $guarded = ['id'];
    protected $auditColumn = true;

    protected $fakeColumns = [];

    public function creator()
    {
        return $this->morphTo();
    }

    public function editor()
    {
        return $this->morphTo();
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the person (resident) associated with the pre-registration.
     * For residential facilities.
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'host_person_id');
    }

    /**
     * Get the resident profile associated with the pre-registration.
     */
    public function residentProfile()
    {
        return $this->belongsTo(ResidentProfile::class, 'resident_profile_id');
    }

    /**
     * Get the facility associated with the pre-registration.
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Generate a unique reference number.
     */
    public static function generateReference()
    {
        return 'PR-' . strtoupper(uniqid());
    }

    /**
     * Generate a QR code for the pre-registration.
     */
    public function getQrCodeAttribute()
    {
        return route('check-in.pre.registered', ['reference' => $this->reference]);
    }

    /**
     * Check if the pre-registration is for today.
     */
    public function isToday()
    {
        return $this->expected_date == now()->format('Y-m-d');
    }

    /**
     * Check if the pre-registration is for a future date.
     */
    public function isFuture()
    {
        return $this->expected_date > now()->format('Y-m-d');
    }

    /**
     * Check if the pre-registration has passed.
     */
    public function isPast()
    {
        return $this->expected_date < now()->format('Y-m-d');
    }

    /**
     * Get the full name of the host.
     */
    public function getHostNameAttribute()
    {
        if ($this->employee) {
            return $this->employee->user->name ?? 'N/A';
        }
        if ($this->person) {
            return $this->person->full_name ?? 'N/A';
        }
        return 'N/A';
    }

    /**
     * Get the host type (Employee or Resident).
     */
    public function getHostTypeAttribute()
    {
        if ($this->employee) {
            return 'employee';
        }
        if ($this->person) {
            return 'resident';
        }
        return 'unknown';
    }
}