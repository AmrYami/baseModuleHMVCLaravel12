<?php

namespace Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status == 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withMessage('Your account waiting for activation');
        } elseif (Auth::check() && Auth::user()->freeze == 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withMessage('Your account has been blocked');
        } elseif (Auth::check() && Auth::user()->banned_until && now()->lessThan(Auth::user()->banned_until)) {
            $banned_days = (int) ceil(now()->floatDiffInDays(Auth::user()->banned_until));
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $message = $banned_days > 0
                ? 'Your account has been suspended for '.$banned_days.' '.Str::plural('day', $banned_days).'. Please contact administrator.'
                : 'Your account has been temporarily suspended. Please contact administrator.';

            return redirect()->route('login')->withMessage($message);
        }

        return $next($request);
    }
}
