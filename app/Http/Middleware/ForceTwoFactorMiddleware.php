<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceTwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (
            $user &&
            is_null($user->two_factor_secret) &&                             // User hasn't enabled 2FA
            !$request->is('user/two-factor-authentication*') &&             // Exclude Jetstream 2FA setup
            !$request->is('logout') &&
            !$request->routeIs('two-factor.required')
        ) {
            return redirect()->route('two-factor.required');
        }

        return $next($request);
    }
}
