<?php
/**
 * arquivo responsável por carregar todas as rotas da API automaticamente
 * baseado na estrutura de pastas dentro de routes/api/{version}/{type}
 * onde {version} é a versão da API (v1, v2, etc)
 * e {type} é o tipo de rota (public ou private)
 * criado por Murilo Dark 
 * data: 2024-06-27
 */

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

$versions = ['v1']; 

foreach ($versions as $version) {

    Route::prefix("api/{$version}")->group(function () use ($version) {

        // 🔓 Rotas públicas
        Route::prefix('public')->group(function () use ($version) {
            $path = __DIR__ . "/api/{$version}/public";

            if (is_dir($path)) {
                foreach (File::allFiles($path) as $file) {
                    require $file->getPathname();
                }
            }
        });

        // 🔐 Rotas privadas
        Route::prefix('private')
            ->middleware('auth:sanctum')
            ->group(function () use ($version) {

                $path = __DIR__ . "/api/{$version}/private";

                if (is_dir($path)) {
                    foreach (File::allFiles($path) as $file) {
                        require $file->getPathname();
                    }
                }
            });

    });
}
