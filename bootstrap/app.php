<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register route middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'profile.completed' => \App\Http\Middleware\EnsureProfileCompleted::class,
            'guest.expiration' => \App\Http\Middleware\CheckGuestExpiration::class,
            'staff.activity' => \App\Http\Middleware\LogStaffActivity::class,
            
        ]);

        
        // Apply to web routes except the profile completion route itself via explicit route middleware
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EnsureProfileCompleted::class,
            \App\Http\Middleware\CheckGuestExpiration::class,
            \App\Http\Middleware\LogStaffActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
