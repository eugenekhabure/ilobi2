<?php
namespace App\Models;

use App\Models\Employee;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Shipu\Watchable\Traits\HasModelEvents;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use Notifiable, InteractsWithMedia, HasModelEvents, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 'password', 'phone', 'address', 
        'roles', 'device_token','web_token', 'status', 'country_code', 'country_code_name', 
        'my_role', 'organization_id', 'facility_id', 'person_id',
        'google_access_token', 'google_refresh_token', 'google_token_expires_at',
    ];
 
    protected $guard_name = 'web';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'options' => 'array',
        'two_factor_enabled' => 'boolean',
        'google_token_expires_at' => 'datetime',
    ];

    protected $appends = ['myrole'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('user'))) {
            return asset($this->getFirstMediaUrl('user'));
        }
        return asset('assets/img/default/user.png');
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->device_token;
    }

    public function getMyroleAttribute()
    {
        return $this->roles->pluck('id', 'id')->first();
    }

    public function getrole()
    {
        return $this->hasOne(Role::class, 'id', 'myrole');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function getMyStatusAttribute()
    {
        return trans('statuses.' . $this->status);
    }

    // ============================================
    // 🚀 NEW RELATIONSHIPS
    // ============================================

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class, 'performed_by');
    }

    // ============================================
    // 🔐 TWO-FACTOR AUTHENTICATION METHODS
    // ============================================

    /**
     * Check if 2FA is enabled for the user.
     */
    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled && !is_null($this->two_factor_secret);
    }

    /**
     * Enable 2FA for the user.
     */
    public function enableTwoFactor($secret)
    {
        $this->two_factor_secret = $secret;
        $this->two_factor_enabled = true;
        $this->two_factor_enabled_at = now();
        $this->two_factor_confirmed_at = now();
        $this->save();
    }

    /**
     * Disable 2FA for the user.
     */
    public function disableTwoFactor()
    {
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_enabled = false;
        $this->two_factor_confirmed_at = null;
        $this->two_factor_enabled_at = null;
        $this->save();
    }

    /**
     * Generate recovery codes for the user.
     */
    public function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        $this->two_factor_recovery_codes = json_encode($codes);
        $this->save();
        return $codes;
    }

    /**
     * Get the user's recovery codes.
     */
    public function getRecoveryCodes()
    {
        if (empty($this->two_factor_recovery_codes)) {
            return [];
        }
        return json_decode($this->two_factor_recovery_codes, true);
    }

    /**
     * Verify a recovery code.
     */
    public function verifyRecoveryCode($code)
    {
        $codes = $this->getRecoveryCodes();
        $index = array_search($code, $codes);
        if ($index !== false) {
            unset($codes[$index]);
            $this->two_factor_recovery_codes = json_encode(array_values($codes));
            $this->save();
            return true;
        }
        return false;
    }
}