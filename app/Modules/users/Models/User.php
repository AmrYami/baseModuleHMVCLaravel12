<?php

namespace Users\Models;

use App\Enum\DefaultValuesEnum;
use App\Models\BaseAuthModel;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Passport\HasApiTokens;
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

    /**
     * Resolve the user for Passport's Password Grant by email OR mobile.
     *
     * Madrasty users register with both `email` and `mobile` (both unique).
     * The Flutter app may post either as the OAuth `username`, and Passport
     * calls this method to look the user up.
     *
     * Lives on this model — not just on App\Models\User — because
     * config/auth.php's `users` provider maps to \Users\Models\User::class
     * and Passport calls findForPassport() on the configured provider's
     * model, not on App\Models\User.
     *
     * Passport's UserRepository invokes this with the OAuth client entity
     * as the second argument; we accept it but currently don't restrict
     * users by client. The closure around the email/mobile OR is required
     * so SoftDeletes' global `deleted_at IS NULL` scope binds correctly:
     * without it, SQL operator precedence would let a soft-deleted user
     * authenticate when their mobile happens to match.
     */
    public function findForPassport(
        string $username,
        ?\League\OAuth2\Server\Entities\ClientEntityInterface $client = null,
    ): ?self {
        return static::query()
            ->where(function ($q) use ($username): void {
                $q->where('email', $username)
                    ->orWhere('mobile', $username);
            })
            ->first();
    }
}
