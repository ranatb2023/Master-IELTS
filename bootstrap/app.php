<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'admin.role' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'check.active' => \App\Http\Middleware\CheckUserActive::class,
            'check.expiration' => \App\Http\Middleware\CheckPackageExpiration::class,
            'feature' => \App\Http\Middleware\CheckFeatureAccess::class,
        ]);

        // Exclude Stripe webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            '/stripe/webhook',
        ]);

        // Only apply CheckUserActive to all web routes
        // CheckPackageExpiration should only be applied to specific routes if needed
        $middleware->web(append: [
            \App\Http\Middleware\CheckUserActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
