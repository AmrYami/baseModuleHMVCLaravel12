<?php

namespace Users\Models;

use App\Enum\DefaultValuesEnum;
use App\Models\ApplicationStatus;
use App\Models\BaseAuthModel;
use App\Models\Exam\Exam;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Users\Models\RequestModel;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class User extends BaseAuthModel implements HasMedia, JWTSubject
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
        'IBAN',
        'agrement', 'teammember', 'title',  'topic', 'teamleader', 'abstract', 'national_id', 'nationality', 'religion', 'city', 'educational',


        'first_name', 'second_name', 'last_name', 'commitments_description', 'speciality',

        'created_by'
    ];

//
//Doctor Video URLs(multi)
    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = [
        'name', 'first_name', 'second_name', 'last_name'
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

    protected $appends = [
        'hashid',
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function fullName(): Attribute
    {
        return Attribute::make(get: function (): string {
            return $this->first_name . " " . $this->second_name . " " . $this->last_name;
        });
    }

    public function fullArName():Attribute
    {
        return Attribute::make(get: function (): string {
            return $this?->getTranslation('first_name', 'ar') . ' '. $this?->getTranslation('second_name', 'ar') . ' '. $this?->getTranslation('last_name', 'ar');
        });
    }


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
//    public function sendPasswordResetNotification($token)
//    {
//        // Your own implementation.
//        $this->notify(new ResetPassword($token));
//    }


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
