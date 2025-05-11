<?php

use Laratrust\Middleware\Role;
use Laratrust\Middleware\Ability;
use Laratrust\Middleware\Permission;
use Illuminate\Foundation\Application;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\BusinessPermission;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HasBusinessMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            HasBusinessMiddleware::class,
        ]);
        $middleware->alias([
            'super' => SuperAdminMiddleware::class,
            'role' => Role::class,
            'permission' => Permission::class,
            'ability' => Ability::class,
            'business.permission' => BusinessPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
