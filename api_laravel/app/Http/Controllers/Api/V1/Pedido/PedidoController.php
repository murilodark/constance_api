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

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function index(PedidoFilterRequest $request)
    {
        $filter = $request->input('filter');
        $perPage = $request->input('per_page', 10);
        $currentPage = $request->input('page', 1);

        // Carrega com as relações necessárias para a visão do Admin
        $query = \App\Models\Pedido::query()->with(['vendedor', 'fornecedor', 'itens.produto']);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                // Busca pelo ID do pedido
                $q->where('id', 'LIKE', "%{$filter}%")
                    // Busca pelo nome do Vendedor
                    ->orWhereHas('vendedor', function ($sub) use ($filter) {
                        $sub->where('nome', 'LIKE', "%{$filter}%");
                    })
                    // Busca pelo nome do Fornecedor
                    ->orWhereHas('fornecedor', function ($sub) use ($filter) {
                        $sub->where('nome', 'LIKE', "%{$filter}%");
                    })
                    // Busca pelo status
                    ->orWhere('status', 'LIKE', "%{$filter}%");
            });
        }

        $list = $query->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $currentPage);

        return $this->ReturnJson($list, 'Listagem global de pedidos', true, 200);
    }


    public function store(StorePedidoRequest $request)
    {
        try {
            // O ID do usuário (users_id) é injetado através do Service usando $request->user()
            $pedido = $this->pedidoService->criarPedido($request->all(), $request->user());
            return $this->ReturnJson($pedido, 'Pedido realizado com sucesso.', true, 201);
        } catch (\Exception $e) {
            return $this->ReturnJson(null, $e->getMessage(), false, 400);
        }
    }

    public function show($id)
    {
        $pedido = \App\Models\Pedido::with(['itens.produto', 'fornecedor', 'vendedor'])->find($id);
        if (!$pedido) return $this->ReturnJson(null, 'Pedido não encontrado.', false, 404);
        return $this->ReturnJson($pedido, 'Pedido detalhado.', true, 200);
    }

    /**
     * Atualiza apenas o status do pedido (Ação Admin)
     * PATCH /api/v1/admin/pedidos/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        // Validação rápida diretamente no método
        $request->validate(['status' => 'required|in:Pendente,Concluído,Cancelado']);

        $pedido = \App\Models\Pedido::findOrFail($id);
        $pedido->update(['status' => $request->status]);

        return $this->ReturnJson($pedido, 'Status atualizado com sucesso!', true, 200);
    }

    /**
     * Retorna todos os pedidos de um fornecedor (via CNPJ)
     * Rota: GET /api/v1/fornecedor/{cnpj}/pedidos
     */
    public function listarPorFornecedor(PedidoFilterRequest $request, $cnpj)
    {
        try {
            $pedidos = $this->pedidoService->listarPorCnpj(
                $cnpj,
                $request->validated(), // Usar dados validados do Request
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
     * Lista os pedidos do vendedor logado com paginação e filtros
     * GET /v1/pedidos/meus-pedidos
     */
    public function meusPedidos(PedidoFilterRequest $request)
    {
        try {
            $user = $request->user();

            // Captura de filtros validados
            $filter = $request->input('filter');
            $status = $request->input('status');
            $dataIni = $request->input('data_ini');
            $dataFim = $request->input('data_fim');
            $perPage = $request->input('per_page', 30);

            $pedidos = $user->pedidos()
                ->with(['itens.produto', 'fornecedor'])
                ->latest()
                // Filtro de Texto (ID, Status ou Observação)
                ->when($filter, function ($query, $filter) {
                    $query->where(function ($q) use ($filter) {
                        $q->where('id', 'like', "%{$filter}%")
                            ->orWhere('observacao', 'like', "%{$filter}%");
                    });
                })
                // Filtro de Status Específico
                ->when($status, fn($q) => $q->where('status', $status))
                // Filtro por Período de Data
                ->when($dataIni, fn($q) => $q->whereDate('created_at', '>=', $dataIni))
                ->when($dataFim, fn($q) => $q->whereDate('created_at', '<=', $dataFim))
                ->paginate($perPage);

            return $this->ReturnJson($pedidos, 'Seus pedidos foram recuperados.', true, 200);
        } catch (Exception $e) {
            return $this->ReturnJson(null, 'Erro ao processar listagem.', false, 500);
        }
    }

    /**
     * POST /api/v1/pedidos/report-diario
     */
    public function dispararReportDiario(Request $request)
    {
        try {
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


    /**
     * Retorna estatísticas de pedidos (quantidade e valor financeiro)
     * Garante retorno zero para status sem registros.
     * Período: Últimos 7 dias
     */
    public function obterEstatisticas(Request $request)
    {
        try {
            $user = $request->user();
            $seteDiasAtras = now()->subDays(7)->startOfDay();

            // 1. Define todos os status possíveis conforme seu banco de dados
            $statusPadrao = [
                'Pendente'  => ['status' => 'Pendente', 'quantidade' => 0, 'total_valor' => 0],
                'Concluído' => ['status' => 'Concluído', 'quantidade' => 0, 'total_valor' => 0],
                'Cancelado' => ['status' => 'Cancelado', 'quantidade' => 0, 'total_valor' => 0],
            ];

            // 2. Inicia a consulta
            $query = \App\Models\Pedido::where('created_at', '>=', $seteDiasAtras);

            // Regra: Se não for admin, filtra pelo usuário logado
            if (!$user->isAdmin()) {
                $query->where('users_id', $user->id);
            }

            // 3. Busca os dados reais do banco
            $dadosBanco = $query->selectRaw('
                    status, 
                    COUNT(*) as quantidade, 
                    SUM(valor_total) as total_valor
                ')
                ->groupBy('status')
                ->get()
                ->keyBy('status'); // Organiza o resultado pelo nome do status

            // 4. Mescla o padrão com o que veio do banco
            // O array_replace substitui os valores do statusPadrao pelos do banco se existirem
            $resumoFinal = array_values(array_replace($statusPadrao, $dadosBanco->toArray()));

            return $this->ReturnJson([
                'escopo' => $user->isAdmin() ? 'global' : 'pessoal',
                'periodo' => [
                    'desde' => $seteDiasAtras->format('d/m/Y'),
                    'ate' => now()->format('d/m/Y')
                ],
                'resumo' => $resumoFinal
            ], 'Métricas consolidadas recuperadas.', true, 200);
        } catch (\Exception $e) {
            return $this->ReturnJson(null, 'Erro: ' . $e->getMessage(), false, 500);
        }
    }
}
