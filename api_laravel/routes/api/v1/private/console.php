<?php

use Illuminate\Support\Facades\Schedule;
use App\Services\PedidoService;
use App\Models\User;

/**
 * Tarefa Programada: Resumo de pedidos dos últimos 7 dias
 * Executa todos os dias às 08:00
 */
Schedule::call(function (PedidoService $service) {
    // Busca administradores ativos para receber o relatório global
    $admins = User::where('tipo', 'admin')->where('status', 'ativo')->get();

    foreach ($admins as $admin) {
        $service->enviarResumoPedidos($admin);
    }
})->dailyAt('08:00')->timezone('America/Sao_Paulo');
