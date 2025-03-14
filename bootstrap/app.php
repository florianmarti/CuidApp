<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\CompleteExpiredContracts;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'EnsureEmailIsConfirmed' => \App\Http\Middleware\EnsureEmailIsConfirmed::class,
            'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
            'not.inactive' => \App\Http\Middleware\EnsureNotInactive::class, // Añadido
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands([
        CompleteExpiredContracts::class,
    ])
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('contracts:complete-expired')->daily();
    })
    ->create();
