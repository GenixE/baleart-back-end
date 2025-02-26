<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(   // Rutes presents a 'web.php' i 'api.php'
        using: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
    )

    ->withMiddleware(function (Middleware $middleware) {
        // Registrar middleware personalitzat
        $middleware->alias([
            'CheckRoleAdmin' => \App\Http\Middleware\CheckRoleAdmin::class,
            'ApiKeyMiddleware' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);

        // Aplicar middleware específic per a API
        $middleware->api("throttle:api");
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // Manejar excepcions per a rutes API
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'No hem trobat elements.'
                ], 404);
            }
        });
    })
    ->create();
