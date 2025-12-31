<?php

use App\Http\Controllers\Api\V1\Pedido\PedidoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.permission:pedidos'])->prefix('v1')->group(function () {
    
    // Agrupamento de Pedidos
    Route::prefix('pedidos')->group(function () {
        // Rotas de Escopo Pessoal e Jobs (Devem vir antes do {id})
        Route::get('/meus-pedidos', [PedidoController::class, 'meusPedidos']);
        Route::post('/report-diario', [PedidoController::class, 'dispararReportDiario']);
        Route::get('/estatisticas', [PedidoController::class, 'obterEstatisticas']);


        // CRUD de Pedidos
        Route::get('/', [PedidoController::class, 'index']);
        Route::post('/', [PedidoController::class, 'store']);
        Route::put('/{id}/status', [PedidoController::class, 'updateStatus']);
        Route::get('/{id}', [PedidoController::class, 'show']);
        Route::put('/{id}', [PedidoController::class, 'update']);
        Route::delete('/{id}', [PedidoController::class, 'destroy']);
    });

    // Agrupamento de Consultas de Fornecedores (Relacionadas a Pedidos)
    Route::prefix('fornecedor')->group(function () {
        Route::get('/{cnpj}/pedidos', [PedidoController::class, 'listarPorFornecedor']);
    });

});
