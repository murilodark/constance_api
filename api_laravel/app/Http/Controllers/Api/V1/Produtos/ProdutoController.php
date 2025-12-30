<?php

namespace App\Http\Controllers\Api\V1\Produtos;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Produto\ProdutoFilterRequest;
use App\Http\Requests\V1\Produto\StoreProdutoRequest;
use App\Http\Requests\V1\Produto\UpdateProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{



    /**
     * Listar produtos
     */
    public function index(ProdutoFilterRequest $request)
    {
        $filter = $request->input('filter');
        $perPage = $request->input('per_page', 10); // Número de itens por página
        $currentPage = $request->input('page', 1); // Página atual

        $query = Produto::query()->with('fornecedor');

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('nome', 'LIKE', "%{$filter}%")
                    ->orWhere('referencia', 'LIKE', "%{$filter}%");

                // Verifica se o filtro é um número para buscar pelo ID
                if (is_numeric($filter)) {
                    $q->orWhere('id', $filter);
                }
            });
        }

        // Aplica a ordenação e a paginação
        $list_produtos = $query->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $currentPage);
        return $this->ReturnJson($list_produtos, 'Listagem de produtos', true, 200);
    }

    /**
     * Criar produto
     */
    public function store(StoreProdutoRequest $request)
    {
        $produto = Produto::create([
            'referencia'      => $request->referencia,
            'nome'            => $request->nome,
            'cor'             => $request->cor,
            'preco'           => $request->preco,
            'fornecedores_id' => $request->fornecedores_id,
        ]);

        return $this->ReturnJson(
            $produto,
            'Produto criado com sucesso.',
            true,
            201
        );
    }

    /**
     * Exibir produto
     */
    public function show(Produto $produto)
    {
        return $this->ReturnJson(
            $produto->load('fornecedor'),
            'Produto encontrado.'
        );
    }

    /**
     * Atualizar produto
     */
    public function update(UpdateProdutoRequest $request, $produto)
    {
        $validated = $request->validated();
        $produto = Produto::findOrFail($produto);
        $produto->update($validated);
        return $this->ReturnJson($produto, 'Sucesso!', true, 200);
    }

    /**
     * Remover produto
     */
    public function destroy(Produto $produto)
    {
        $produto->delete();
        return $this->ReturnJson(
            null,
            'Produto removido com sucesso.'
        );
    }


    public function uploadCsv(Request $request, $fornecedorId)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        // Armazena temporariamente o arquivo
        $path = $request->file('arquivo')->store('uploads/csv');

        // Despacha o Job para a fila (Queue)
        \App\Jobs\ProcessarUploadProdutos::dispatch($path, $fornecedorId, $request->user());

        return $this->ReturnJson(null, 'O processamento da planilha foi iniciado. Você receberá um e-mail ao finalizar.', true, 202);
    }
}
