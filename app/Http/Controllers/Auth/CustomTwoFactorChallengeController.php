<?php

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

        if ($user && $user->two_factor_secret) {
            try {
                $secret = decrypt($user->two_factor_secret);
                $code = (new Google2FA)->getCurrentOtp($secret);
            } catch (\Throwable $exception) {
                \Log::warning('Failed to decrypt 2FA secret', [
                    'user_id' => $user->id,
                    'error' => $exception->getMessage(),
                ]);
                $user->forceFill([
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                    'two_factor_confirmed_at' => null,
                ])->save();

                return app(TwoFactorChallengeViewResponse::class);
            }

            Mail::raw("Your 2FA code is: $code", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Two-Factor Authentication Code');
            });
        }

        return app(TwoFactorChallengeViewResponse::class);
    }
}
