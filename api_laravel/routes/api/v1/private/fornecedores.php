<?php

use App\Http\Controllers\Api\V1\Fornecedor\FornecedorController;
use App\Http\Controllers\Api\V1\Produtos\ProdutoController; // Importar o ProdutoController
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.permission:fornecedores'])
    ->prefix('v1/fornecedores')
    ->group(function () {

        Route::get('/', [FornecedorController::class, 'index']);
        Route::post('/', [FornecedorController::class, 'store']);

        // Agrupamento por ID do fornecedor
        Route::prefix('{fornecedor}')->group(function () {
            Route::get('/', [FornecedorController::class, 'show']);
            Route::put('/', [FornecedorController::class, 'update']);
            Route::delete('/', [FornecedorController::class, 'destroy']);

            // Sub-recursos de Produtos vinculados ao Fornecedor
            Route::get('/produtos', [FornecedorController::class, 'listaProdutos']);

            // ota para upload de CSV
            // URL: POST /api/v1/fornecedores/{fornecedor}/produtos/upload
            Route::post('/produtos/upload', [ProdutoController::class, 'uploadCsv']);
        });
    });
