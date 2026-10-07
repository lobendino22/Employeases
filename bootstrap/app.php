<?php

use App\Http\Middleware\ArchiveExpiredJobVacancies;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Sweep expired vacancies on every request so archiving no longer depends
        // on the scheduler cron being configured or on an admin signing in.
        $middleware->append(ArchiveExpiredJobVacancies::class);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
