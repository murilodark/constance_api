<?php

namespace App\Http\Controllers\Api\V1\Fornecedor;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Fornecedor\FornecedorFilterRequest;
use App\Http\Requests\V1\Fornecedor\StoreFornecedorRequest;
use App\Http\Requests\V1\Fornecedor\UpdateFornecedorRequest;
use App\Http\Requests\V1\Fornecedor\FornecedorDeleteRequest;
use App\Models\Fornecedor;
use Illuminate\Support\Facades\DB;

class FornecedorController extends Controller
{
    /**
     * Listar fornecedores
     */
    public function index(FornecedorFilterRequest $request)
    {
        $filter = $request->input('filter');
        $perPage = $request->input('per_page', 10);
        $currentPage = $request->input('page', 1);

        $query = Fornecedor::query()->with('enderecos');

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('nome', 'LIKE', "%{$filter}%")
                  ->orWhere('cnpj', 'LIKE', "%{$filter}%");

                if (is_numeric($filter)) {
                    $q->orWhere('id', $filter);
                }
            });
        }

        $list = $query->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $currentPage);
        return $this->ReturnJson($list, 'Listagem de fornecedores', true, 200);
    }

    /**
     * Criar fornecedor e endereço (Passo 2 do fluxo)
     */
    public function store(StoreFornecedorRequest $request)
    {
        DB::beginTransaction();
        try {
            $fornecedor = Fornecedor::create($request->only(['nome', 'cnpj', 'status']));

            if ($request->has('endereco')) {
                $fornecedor->enderecos()->create($request->endereco);
            }

            DB::commit();
            return $this->ReturnJson($fornecedor->load('enderecos'), 'Fornecedor criado com sucesso.', true, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ReturnJson(null, 'Erro ao salvar fornecedor.', false, 500);
        }
    }

    /**
     * Exibir fornecedor (Injeção direta pelo Model Binding)
     */
    public function show(Fornecedor $fornecedor)
    {
        return $this->ReturnJson(
            $fornecedor->load('enderecos'),
            'Fornecedor encontrado.'
        );
    }

    /**
     * Atualizar fornecedor e endereço
     */
    public function update(UpdateFornecedorRequest $request, Fornecedor $fornecedor)
    {
        DB::beginTransaction();
        try {
            $fornecedor->update($request->validated());

            if ($request->has('endereco')) {
                // Atualiza o endereço existente ou cria se não houver
                $fornecedor->enderecos()->updateOrCreate(
                    ['fornecedores_id' => $fornecedor->id],
                    $request->endereco
                );
            }

            DB::commit();
            return $this->ReturnJson($fornecedor->load('enderecos'), 'Fornecedor atualizado com sucesso.', true, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->ReturnJson(null, 'Erro ao atualizar fornecedor.', false, 500);
        }
    }

    /**
     * Remover fornecedor
     */
    public function destroy(FornecedorDeleteRequest $request, Fornecedor $fornecedor)
    {
        $fornecedor->delete();
        return $this->ReturnJson(null, 'Fornecedor removido com sucesso.');
    }
}
