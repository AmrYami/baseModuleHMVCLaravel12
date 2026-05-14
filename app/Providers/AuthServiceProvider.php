<?php

namespace App\Providers;

use App\Models\Team;
use App\Policies\TeamPolicy;
use DateInterval;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',

        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(new DateInterval('P1Y'));
        Passport::personalAccessTokensExpireIn(new DateInterval('P1Y'));
        Passport::refreshTokensExpireIn(new DateInterval('P2Y'));

        // make CRM Admin has no any restrictions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('CRM Admin') ? true : null;
        });
    }
}
