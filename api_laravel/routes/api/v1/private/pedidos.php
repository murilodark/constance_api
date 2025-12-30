<?php

use App\Http\Controllers\Api\V1\Pedido\PedidoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.permission:pedidos'])->prefix('v1')->group(function () {
    
    // Rota solicitada: Retorna pedidos pelo CNPJ do fornecedor
    Route::get('/fornecedor/{cnpj}/pedidos', [PedidoController::class, 'listarPorFornecedor']);
    
    // Demais rotas de pedidos
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    Route::put('/pedidos/{id}', [PedidoController::class, 'update']);
    Route::delete('/pedidos/{id}', [PedidoController::class, 'destroy']);
    Route::post('/pedidos/report-diario', [PedidoController::class, 'dispararReportDiario']);

});
