<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthService
{
    /**
     * Mapeamento de permissões granulares por Tipo -> Módulo -> Métodos
     */
    private array $permissoes = [
        'admin' => [
            'users'        => ['index', 'store', 'show', 'update', 'destroy'],
            'fornecedores' => ['index', 'store', 'show', 'update', 'destroy', 'listaProdutos', 'uploadCsv', 'listarPorFornecedor'],
            'produtos'     => ['index', 'store', 'show', 'update', 'destroy'],
            'vinculos'     => ['index', 'vincular', 'desvincular'],
            'pedidos' => ['index', 'store', 'show', 'update', 'destroy','listarPorFornecedor','dispararReportDiario','meusPedidos','obterEstatisticas','updateStatus'],
        ],
        'vendedor' => [
            'produtos'     => ['index', 'show'], // Vendedor só visualiza produtos
            'vinculos'     => ['index'],         // Vendedor só vê seus vínculos
            'pedidos' => [ 'store', 'show','listarPorFornecedor','dispararReportDiario','meusPedidos','obterEstatisticas'], // Vendedor pode criar e ver
            'fornecedores' => ['index', 'listaProdutos', 'listarPorFornecedor'], // Vendedor pode ver fornecedores e seus produtos
        ]
    ];

    /**
     * Valida permissão por Módulo e Método
     */
    public function checkPermission(string $modulo, string $metodo): void
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        if (!$authUser || !$authUser->isAtivo()) {
            throw new Exception("Acesso negado. Usuário inativo ou não autenticado.");
        }

        $tipo = $authUser->tipo;

        // 1. Verifica se o perfil existe
        if (!isset($this->permissoes[$tipo])) {
            throw new Exception("Perfil de usuário '{$tipo}' não possui permissões configuradas.");
        }

        // 2. Verifica se tem acesso ao módulo e ao método específico
        $temPermissao = isset($this->permissoes[$tipo][$modulo]) &&
            in_array($metodo, $this->permissoes[$tipo][$modulo]);

        if (!$temPermissao) {
            throw new Exception("Ação não autorizada. O perfil '{$tipo}' não tem permissão para '{$metodo}' no módulo '{$modulo}'.");
        }
    }
}
