<?php

namespace App\Services;

use App\Models\Fornecedor;
use Exception;

class FornecedorService
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Lista os produtos de um fornecedor específico com paginação
     */
    public function listarProdutosPorFornecedor(int $fornecedorId, int $perPage = 10, int $currentPage = 1)
    {
        $fornecedor = Fornecedor::findOrFail($fornecedorId);

        return $fornecedor->produtos()
            ->orderBy('id', 'DESC')
            ->paginate($perPage, ['*'], 'page', $currentPage);
    }

    /**
     * Outros métodos do domínio de fornecedor podem vir aqui (criar, deletar, etc)
     */
}
