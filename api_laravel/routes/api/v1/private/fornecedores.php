<?php

use App\Http\Controllers\Api\V1\Fornecedor\FornecedorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('v1/fornecedores')
    ->group(function () {
        Route::get('/', [FornecedorController::class, 'index']);
        Route::post('/', [FornecedorController::class, 'store']);
        Route::get('{fornecedor}', [FornecedorController::class, 'show']);
        Route::put('{fornecedor}', [FornecedorController::class, 'update']);
        Route::delete('{fornecedor}', [FornecedorController::class, 'destroy']);
    });
