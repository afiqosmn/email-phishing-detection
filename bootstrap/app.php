<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies for reverse proxy environments (Railway, etc.)
        // This ensures HTTPS is recognized when behind a load balancer
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'auth'           => \App\Http\Middleware\Authenticate::class,
            'google.refresh' => \App\Http\Middleware\RefreshGoogleToken::class,
            'ensure.google'  => \App\Http\Middleware\EnsureGoogleAuthenticated::class,
            'admin'          => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
