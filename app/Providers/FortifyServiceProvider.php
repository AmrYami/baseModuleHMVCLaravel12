<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\LoginWithEmailOrUsername;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse as CustomLoginResponse;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
//        Fortify::createUsersUsing(UsersController::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            $key = Str::lower((string) $request->input('email')).'|'.$request->ip();

            return [
                Limit::perMinute(3)->by($key),
                Limit::perHour(10)->by($key),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            $key = Str::lower((string) $request->input('email')).'|'.$request->ip();

            return [
                Limit::perMinute(3)->by($key),
                Limit::perHour(10)->by($key),
            ];
        });

        Fortify::authenticateUsing(new LoginWithEmailOrUsername);
//        Fortify::loginView(function () {
//            return view('auth.login');
//        });
        Fortify::registerView(function () {
            return view('auth.register');
        });
        $this->app->singleton(
            LoginResponseContract::class,
            CustomLoginResponse::class
        );

//        Fortify::authenticateUsing(function (Request $request) {
//            if ($request->is('doctor/*')) {
//                $doctor = Doctor::where('email', $request->email)->first();
//                if ($doctor && Hash::check($request->password, $doctor->password)) {
//                    return $doctor;
//                }
//            }
//            // Otherwise, default behavior for users
//        });


    }
}
