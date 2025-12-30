<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class FornecedorVinculoService
{


  

    public function vincularVendedorAoFornecedor(int $vendedorId, int $fornecedorId): array
    {
        $vendedor = User::findOrFail($vendedorId);

        // Validação de duplicidade
        $jaVinculado = $vendedor->fornecedores()
            ->where('fornecedores_id', $fornecedorId)
            ->exists();

        if ($jaVinculado) {
            return [
                'success' => false,
                'message' => 'Este fornecedor já está vinculado ao vendedor informado.'
            ];
        }

        // Realiza o vínculo e recupera os dados da tabela pivô
        $vendedor->fornecedores()->attach($fornecedorId);

        // Busca o registro recém criado para retornar os dados (id, created_at, etc)
        $vinculo = DB::table('users_has_fornecedores')
            ->where('users_id', $vendedorId)
            ->where('fornecedores_id', $fornecedorId)
            ->first();

        return [
            'success' => true,
            'message' => 'Fornecedor vinculado com sucesso ao vendedor.',
            'data'    => $vinculo
        ];
    }


    /**
     * Remove o vínculo entre vendedor e fornecedor
     */
    public function desvincularVendedorDoFornecedor(int $vendedorId, int $fornecedorId): array
    {
        $vendedor = User::findOrFail($vendedorId);

        // Verifica se o vínculo realmente existe antes de tentar deletar
        $jaVinculado = $vendedor->fornecedores()
            ->where('fornecedores_id', $fornecedorId)
            ->exists();

        if (!$jaVinculado) {
            return [
                'success' => false,
                'message' => 'Este fornecedor não está vinculado a este vendedor.'
            ];
        }

        // Realiza o desvínculo (remove a linha da tabela users_has_fornecedores)
        $vendedor->fornecedores()->detach($fornecedorId);

        return [
            'success' => true,
            'message' => 'Fornecedor desvinculado com sucesso.',
            'data'    => ['users_id' => $vendedorId, 'fornecedores_id' => $fornecedorId]
        ];
    }

    /**
     * Lista os fornecedores vinculados a um usuário específico
     */
    public function listarFornecedoresDoUsuario(int $userId): array
    {
        // Verifica se o usuário existe
        $user = User::findOrFail($userId);

        // Retorna a coleção de fornecedores vinculados
        // Usamos o relacionamento que definimos no Model User
        $fornecedores = $user->fornecedores()->get();

        return [
            'success' => true,
            'message' => $fornecedores->isEmpty()
                ? 'Nenhum fornecedor vinculado a este usuário.'
                : 'Fornecedores listados com sucesso.',
            'data'    => $fornecedores
        ];
    }
}
