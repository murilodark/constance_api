<?php

use App\Exceptions\ApiExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Middleware\ForceJsonResponse;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // ... (suas rotas permanecem iguais)
        using: function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(function () {
                    foreach (File::allFiles(base_path('routes/api/v1/public')) as $file) {
                        require $file->getPathname();
                    }
                    foreach (File::allFiles(base_path('routes/api/v1/private')) as $file) {
                        Route::middleware('auth:sanctum')->group($file->getPathname());
                    }
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(new ApiExceptionHandler()) // <--- A mágica acontece aqui
    ->create();
