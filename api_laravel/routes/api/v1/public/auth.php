<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Grupo de versão 1 da API
Route::prefix('v1')->group(function () {

    // Rota pública de login
    Route::post('login', [AuthController::class, 'login']);

    // Rotas protegidas por token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
