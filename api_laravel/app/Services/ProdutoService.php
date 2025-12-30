<?php

namespace App\Services;

use App\Models\Produto;
use Exception;

class ProdutoService
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

     /**
     * @param string $metodo (index, store, show, etc)
     */
    public function validarAcesso(string $metodo): void
    {
        // Repassa módulo e método para o centralizador
        $this->authService->checkPermission('produtos', $metodo);
    }

    public function listarTodos()
    {
        return Produto::with('fornecedor')->get();
    }

    public function criar(array $data): Produto
    {
        return Produto::create($data);
    }
}
