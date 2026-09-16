<?php

use App\Http\Middleware\AuditRequests;
use App\Http\Middleware\DecryptRouteIds;
use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'     => EnsureRole::class,
            'active'   => EnsureActiveUser::class,
            'decrypt'  => DecryptRouteIds::class,
        ]);
        // Decrypt encrypted IDs before route model binding (must run before SubstituteBindings)
        $middleware->web(prepend: [DecryptRouteIds::class]);
        // Record every state-changing request into the audit log
        $middleware->web(append: [AuditRequests::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
