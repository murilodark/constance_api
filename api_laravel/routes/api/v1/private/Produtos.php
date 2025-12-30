<?php

use App\Http\Controllers\Api\V1\Produtos\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.permission:produtos'])
    ->prefix('v1/produtos')
    ->group(function () {
        Route::get('/', [ProdutoController::class, 'index']);
        Route::post('/', [ProdutoController::class, 'store']);
        Route::get('{produto}', [ProdutoController::class, 'show']);
        Route::put('{produto}', [ProdutoController::class, 'update']);
        Route::delete('{produto}', [ProdutoController::class, 'destroy']);
    });
