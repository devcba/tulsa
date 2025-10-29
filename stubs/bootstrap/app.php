<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Application $app) {
        $app->middleware([
            // Middleware globales
        ]);

        $app->routeMiddleware([
            // 'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Application $app) {
        // Configuración de excepciones
    })
    ->create();
