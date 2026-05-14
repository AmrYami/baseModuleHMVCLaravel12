<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Concerns\UsesHashids;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Passport\HasApiTokens;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use UsesHashids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'mobile',
        'email_verified_at',
        'status',
        'code',
        'password',
        'type',
    ];

    protected $guard_name = 'web';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'hashid',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setNameAttribute($value): void
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['name'] = json_encode($value);

            return;
        }
        $this->attributes['name'] = json_encode(['en' => (string) $value]);
    }

    public function getNameAttribute($value)
    {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded['en'] ?? reset($decoded) ?? '';
        }

        return $value;
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

    /**
     * Resolve Passport tokens for this legacy web user model through the
     * configured mobile API provider. Both user models map to the users table.
     */
    public function getProviderName(): string
    {
        return (string) config('auth.guards.api.provider', 'users');
    }

    /**
     * Resolve the user for Passport's Password Grant by email OR mobile.
     *
     * Madrasty users register with both `email` and `mobile` (both unique).
     * The Flutter app posts whichever they prefer as the OAuth `username`,
     * and Passport calls this method to look the user up.
     *
     * Mirror of the implementation on \Users\Models\User (which is the
     * actual model used by the `api` Passport provider). Closure wrapping
     * + nullable Client param are required for the same reasons noted there.
     */
    public function findForPassport(
        string $username,
        ?ClientEntityInterface $client = null,
    ): ?self {
        return static::query()
            ->where(function ($q) use ($username): void {
                $q->where('email', $username)
                    ->orWhere('mobile', $username);
            })
            ->first();
    }
}
