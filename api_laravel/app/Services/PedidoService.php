<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class PedidoService
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function validarAcesso(string $metodo)
    {
        $this->authService->checkPermission('pedidos', $metodo);
    }

    /**
     * Cache de produtos por fornecedor no Redis (Válido por 1 hora)
     */
    public function listarProdutosCache(int $fornecedorId)
    {
        return Cache::remember("produtos_fornecedor_{$fornecedorId}", 3600, function () use ($fornecedorId) {
            return Produto::where('fornecedores_id', $fornecedorId)->get();
        });
    }

    public function criarPedido(array $data, User $user)
    {
        // 1. Validar se o usuário tem acesso ao fornecedor
        $hasAccess = $user->fornecedores()->where('fornecedores_id', $data['fornecedores_id'])->exists();
        if (!$hasAccess) {
            throw new Exception("Você não tem permissão para realizar pedidos para este fornecedor.");
        }

        return DB::transaction(function () use ($data,$user) {
            $pedido = Pedido::create([
                'users_id'        => $user->id, 
                'fornecedores_id' => $data['fornecedores_id'],
                'observacao'      => $data['observacao'] ?? null,
                'status'          => 'Pendente',
                'valor_total'     => 0
            ]);

            $total = 0;
            foreach ($data['produtos'] as $item) {
                // 2. Validar se o produto pertence ao fornecedor (usando cache para agilizar)
                $produto = $this->listarProdutosCache($data['fornecedores_id'])
                    ->where('id', $item['produtos_id'])->first();

                if (!$produto) {
                    throw new Exception("Produto ID {$item['produtos_id']} não pertence ao fornecedor selecionado.");
                }

                $pedido->itens()->create([
                    'produtos_id'    => $item['produtos_id'],
                    'quantidade'     => $item['quantidade'],
                    'valor_unitario' => $produto->preco
                ]);

                $total += ($produto->preco * $item['quantidade']);
            }

            $pedido->update(['valor_total' => $total]);
            return $pedido->load('itens.produto');
        });
    }


    /**
     * Lista todos os pedidos de um fornecedor filtrado pelo CNPJ
     */
    public function listarPorCnpj(string $cnpj, array $filtros, User $user)
    {
        // 1. Busca o fornecedor pelo CNPJ
        $fornecedor = \App\Models\Fornecedor::where('cnpj', $cnpj)->firstOrFail();

        // 2. Valida se o usuário tem acesso a este fornecedor, somente se nao for admin
        if (!$user->isAdmin()) {
            $hasAccess = $user->fornecedores()->where('fornecedores_id', $fornecedor->id)->exists();
            if (!$hasAccess) {
                throw new Exception("Acesso negado aos pedidos deste fornecedor.");
            }
        }
        $query = Pedido::where('fornecedores_id', $fornecedor->id)
            ->with(['itens.produto', 'fornecedor']);

        // 3. Aplica filtros opcionais (Status e Data)
        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (isset($filtros['data_ini'])) {
            $query->whereDate('created_at', '>=', $filtros['data_ini']);
        }

        if (isset($filtros['data_fim'])) {
            $query->whereDate('created_at', '<=', $filtros['data_fim']);
        }

        return $query->orderBy('id', 'DESC')
            ->paginate($filtros['per_page'] ?? 10);
    }

    /**
     * Coleta dados dos últimos 7 dias e envia o resumo
     */
    public function enviarResumoPedidos($user)
    {
        $resumo = \App\Models\Pedido::where('created_at', '>=', now()->subDays(7))
            ->select('status', \DB::raw('count(*) as total'), \DB::raw('sum(valor_total) as soma'))
            ->groupBy('status')
            ->get();

        $user->notify(new \App\Notifications\ResumoPedidosNotification($resumo));

        return $resumo;
    }
}
