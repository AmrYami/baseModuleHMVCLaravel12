<?php

use App\Http\Middleware\CheckBanned;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\SecurityHeaders;
use App\Providers\AliasServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Csp\AddCspHeaders;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AliasServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {

        $trustedProxies = array_filter(array_map('trim', explode(',', (string) env('TRUSTED_PROXIES', '*'))));
        $middleware->trustProxies(at: $trustedProxies ?: '*');
        // global middlewares
        $middleware->append(ForceHttps::class);
        $middleware->append(CheckBanned::class);
        $middleware->append(SecurityHeaders::class);
        $middleware->append(AddCspHeaders::class);

        $middleware->alias([
            // roles and permissions
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            //            'force.2fa', \App\Http\Middleware\ForceTwoFactorMiddleware::class,
            // jwt auth
            'jwt.auth' => Authenticate::class,
            'jwt.refresh' => RefreshToken::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
