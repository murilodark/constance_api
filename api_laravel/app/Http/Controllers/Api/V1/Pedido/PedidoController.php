<?php

namespace App\Http\Controllers\Api\V1\Pedido;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Pedido\PedidoFilterRequest;
use App\Services\PedidoService;
use App\Http\Requests\V1\Pedido\StorePedidoRequest;
use Exception;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService) {
        $this->pedidoService = $pedidoService;
    }

    public function store(StorePedidoRequest $request) {
        try {
            $pedido = $this->pedidoService->criarPedido($request->all(), $request->user());
            return $this->ReturnJson($pedido, 'Pedido realizado com sucesso.', true, 201);
        } catch (\Exception $e) {
            return $this->ReturnJson(null, $e->getMessage(), false, 400);
        }
    }

    public function show($id) {
        $pedido = \App\Models\Pedido::with(['itens.produto', 'fornecedor'])->find($id);
        if (!$pedido) return $this->ReturnJson(null, 'Pedido não encontrado.', false, 404);
        return $this->ReturnJson($pedido, 'Pedido detalhado.', true, 200);
    }

    /**
 * Retorna todos os pedidos de um fornecedor (via CNPJ)
 * Rota: GET /api/v1/fornecedor/{cnpj}/pedidos
 */
public function listarPorFornecedor(PedidoFilterRequest $request, $cnpj)
{
    try {
        // O Middleware check.permission:pedidos já validou o acesso
        $pedidos = $this->pedidoService->listarPorCnpj(
            $cnpj, 
            $request->all(), 
            $request->user()
        );

        return $this->ReturnJson(
            $pedidos, 
            'Pedidos do fornecedor listados com sucesso.', 
            true, 
            200
        );
    } catch (Exception $e) {
        return $this->ReturnJson(null, $e->getMessage(), false, 403);
    }
}

/**
 * POST /api/v1/pedidos/report-diario
 */
public function dispararReportDiario(Request $request)
{
    try {
        // O middleware 'check.permission:pedidos' deve autorizar o método 'dispararReportDiario'
        $this->pedidoService->enviarResumoPedidos($request->user());

        return $this->ReturnJson(
            null, 
            'O resumo dos últimos 7 dias foi enviado para seu e-mail cadastrado.', 
            true, 
            200
        );
    } catch (\Exception $e) {
        return $this->ReturnJson(null, $e->getMessage(), false, 500);
    }
}


}
