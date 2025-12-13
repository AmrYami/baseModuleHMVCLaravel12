<?php
namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginWithEmailOrUsername
{
    public function __invoke(Request $request)
    {
        $login = $request->input('email');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

        // Find user by email or username
        $user = User::where($fieldType, $login)->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            $status = (int) ($user->status ?? 0);
            if ($status === 0) {
                throw ValidationException::withMessages([
                    'email' => __('Your account is pending approval. Please contact the administrator.'),
                ]);
            }

            if ((int) ($user->freeze ?? 0) === 1) {
                throw ValidationException::withMessages([
                    'email' => __('Your account has been freezed.'),
                ]);
            }

            if ($user->banned_until && now()->lessThan($user->banned_until)) {
                $until = $user->banned_until instanceof \Carbon\CarbonInterface
                    ? $user->banned_until
                    : \Carbon\Carbon::parse($user->banned_until);
                $seconds = max(0, $until->getTimestamp() - now()->getTimestamp());
                $daysFloat = number_format($seconds / 86400, 2, '.', '');
                throw ValidationException::withMessages([
                    'email' => __('Your account has been suspended for :days days', ['days' => $daysFloat]),
                ]);
            }

            return $user;
        }

        return null;
    }
}
