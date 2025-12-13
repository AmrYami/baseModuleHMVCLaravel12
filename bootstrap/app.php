<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \App\Providers\AliasServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->trustProxies(at: '*');
        // global middlewares
        $middleware->append(\App\Http\Middleware\CheckBanned::class);
        $middleware->append(\Spatie\Csp\AddCspHeaders::class);

        $examGateMiddleware = class_exists(\Fakeeh\Assessments\Http\Middleware\ExamGate::class)
            ? \Fakeeh\Assessments\Http\Middleware\ExamGate::class
            : \App\Http\Middleware\BypassExamGate::class;

        $middleware->alias([
            // roles and permissions
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
//            'force.2fa', \App\Http\Middleware\ForceTwoFactorMiddleware::class,
            //jwt auth
            'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
            'exam.gate' => $examGateMiddleware,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
