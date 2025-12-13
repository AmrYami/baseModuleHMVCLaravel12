<?php

namespace App\Http\Controllers\Auth;

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use PragmaRX\Google2FA\Google2FA;

class CustomTwoFactorChallengeController extends TwoFactorAuthenticatedSessionController
{
    public function create(TwoFactorLoginRequest $request): TwoFactorChallengeViewResponse
    {
        $user = $request->challengedUser();

//        try {
            if ($user && $user->two_factor_secret) {
                $google2fa = new Google2FA;
                $secret = decrypt($user->two_factor_secret);
                $code = $google2fa->getCurrentOtp($secret);

                // Send code by email
                Mail::raw("Your 2FA code is: $code", function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your Two-Factor Authentication Code');
                });
            }
//        } catch (\Exception $exception) {
//            $u = \App\Models\User::find($user->id);
//            $u->forceFill(['two_factor_secret' => null, 'two_factor_recovery_codes' => null, 'two_factor_confirmed_at' => null,])->save();
//        }

        return app(TwoFactorChallengeViewResponse::class);
    }
}
