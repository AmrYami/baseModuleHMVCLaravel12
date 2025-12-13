<?php

namespace Users\Models;

use App\Models\BaseAuthModel;
use Users\Models\RequestModel;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Doctor extends BaseAuthModel implements HasMedia, JWTSubject
{

    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    use InteractsWithMedia;
    use SoftDeletes;
    use HasTranslations;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use TwoFactorAuthenticatable;

    /**
     * Specify the guard name for Spatie Permission.
     */
    protected $guard_name = 'doctor';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'mobile',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'remember_token',
        'profile_photo_path',
        'current_team_id',
        'language',
        'type',
        'freeze',
        'code',
        'status',
        'approve',
        'banned_until',
        'specialization',
        'hospital',
        'designation',
        'specialty',
        'languages',
        'experience',
        'description',
        'achievements',
        'studies',
        'work_experience',
        'email_verified_at',
        'nationality',
        'slug',
        'published_on',
        'about',
        'book_an_appointment_URL',
        'speciality_text',
        'facilities',
        'clinics',
        'clinic_text',
        'head_of_department',
    ];

//
//Doctor Video URLs(multi)
    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = [
        'name',
        'specialization',
        'hospital',
        'designation',
        'experience',
        'description',
        'achievements',
        'studies',
        'work_experience',
        'speciality_text',
        'facilities',
        'clinics',
        'clinic_text',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $dates = [
        'banned_until'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'languages' => 'array',
        'nationality' => 'array',
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @param $role
     * @return bool
     */
    public function hasGlobalRole($role)
    {
        if ($this->hasRole('CRM Admin')) {
            return true; // Super Admins bypass team checks
        }

        return $this->hasRole($role, $this->currentTeam);
    }


    /**
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Your own implementation.
        $this->notify(new ResetPassword($token));
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownerRequests()
    {
        return $this->hasMany(RequestModel::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function causerRequested()
    {
        return $this->hasMany(RequestModel::class, 'request_from');
    }

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
}

