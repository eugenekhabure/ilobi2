<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessOTP extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'person_id',
        'invitation_id',
        'visitor_id',
        'otp_code',
        'expires_at',
        'used_at',
        'attempts',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isUsed()
    {
        return $this->status === 'used';
    }

    public function isValid()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function markUsed()
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function markExpired()
    {
        $this->update(['status' => 'expired']);
    }

    public function incrementAttempts()
    {
        $this->increment('attempts');
        if ($this->attempts >= 5) {
            $this->update(['status' => 'blocked']);
        }
    }

    public static function generate($facilityId, $personId, $length = 4)
    {
        $code = str_pad(random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);
        
        return self::create([
            'facility_id' => $facilityId,
            'person_id' => $personId,
            'otp_code' => $code,
            'expires_at' => now()->addMinutes(5),
            'status' => 'active',
        ]);
    }
}