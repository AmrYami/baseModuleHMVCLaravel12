<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BypassExamGate
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
