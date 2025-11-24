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
    ->withMiddleware(function (Middleware $middleware): void {
        // Rate limiting par défaut pour toutes les API
        $middleware->throttleApi();
        
        // Aliases pour différents niveaux
        $middleware->alias([
            'throttle.strict' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':10,1',
            'throttle.auth' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,1',
            'throttle.upload' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':20,1',
        ]);

        // Personnaliser les réponses 429
        $middleware->append(\App\Http\Middleware\CustomThrottleResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
