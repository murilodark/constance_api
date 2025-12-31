<?php

use App\Http\Controllers\Api\V1\Users\UserController;
use App\Http\Controllers\Api\V1\Users\UserFornecedorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1/users')->group(function () {

    // Grupo de Permissão: users
    Route::middleware('check.permission:users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
    });

    // Agrupamento para rotas que dependem de um ID de usuário específico
    Route::prefix('{user}')->group(function () {
        
        // Grupo de Permissão: users (Ações individuais)
        Route::middleware('check.permission:users')->group(function () {
            Route::post('/', [UserController::class, 'store']);
            Route::get('/', [UserController::class, 'show']);
            Route::put('/', [UserController::class, 'update']);
            Route::delete('/', [UserController::class, 'destroy']);
        });

        // Sub-recurso: Fornecedores (Grupo de Permissão: vinculos)
        // A URL ficará: POST /v1/users/5/fornecedores
        Route::middleware('check.permission:vinculos')->prefix('fornecedores')->group(function () {
            Route::get('/', [UserFornecedorController::class, 'index']);         // Método: index
            Route::post('/', [UserFornecedorController::class, 'vincular']);      // Método: vincular
            Route::delete('/', [UserFornecedorController::class, 'desvincular']); // Método: desvincular
        });
    });
});
