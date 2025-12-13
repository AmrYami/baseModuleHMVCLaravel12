<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        foreach (['web', 'doctor'] as $guard) {
            $guardInstance = Auth::guard($guard);
            if (!$guardInstance->check()) {
                continue;
            }
            $user = $guardInstance->user();
            $message = null;

            if ((int) $user->status === 0) {
                $message = __('Your account is pending approval. Please contact the administrator.');
            } elseif ((int) $user->freeze === 1) {
                // Manual freeze (forever) — only admin can un-freeze
                $message = __('Your account has been frozen by an administrator.');
            } elseif ($user->banned_until && now()->lessThan($user->banned_until)) {
                // Time-bound freeze (auto-unfreezes after the date)
                try {
                    $until = $user->banned_until instanceof \Carbon\CarbonInterface
                        ? $user->banned_until
                        : \Carbon\Carbon::parse($user->banned_until);
                    $message = __('Your account is frozen until :date.', [
                        'date' => $until->toDayDateTimeString(),
                    ]);
                } catch (\Throwable $e) {
                    $message = __('Your account is temporarily frozen. Please try again later.');
                }
            }

            if ($message) {
                $guardInstance->logout();
                Session::invalidate();
                Session::regenerateToken();
                return redirect()->route('login')->withErrors(['email' => $message]);
            }
        }

        return $next($request);
    }
}
