<?php

use App\Http\Controllers\Api\V1\Users\UserController;
use App\Http\Controllers\Api\V1\Users\UserFornecedorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('v1/users')
    ->group(function () {

        // Rotas de Recurso Principal (User)
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);

        // Agrupamento para rotas que dependem de um ID de usuário específico
        Route::prefix('{user}')->group(function () {
            Route::get('/', [UserController::class, 'show']);
            Route::put('/', [UserController::class, 'update']);
            Route::delete('/', [UserController::class, 'destroy']);

            // Sub-recurso: Fornecedores (Vínculo/Desvínculo)
            // A URL ficará: POST /v1/users/5/fornecedores
             Route::prefix('fornecedores')->group(function () {
                Route::get('/', [UserFornecedorController::class, 'index']);      // Listar
                Route::post('/', [UserFornecedorController::class, 'vincular']);    // Vincular
                Route::delete('/', [UserFornecedorController::class, 'desvincular']); // Desvincular
            });
        });
    });
