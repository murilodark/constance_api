<?php

namespace App\Http\Controllers\Api\V1\Produtos;

use App\Http\Controllers\Controller;
use App\Services\ProdutoService;
use App\Http\Requests\V1\Produto\StoreProdutoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProdutoController extends Controller
{
    protected $produtoService;

    public function __construct(ProdutoService $produtoService)
    {
        $this->produtoService = $produtoService;
    }

    public function index(): JsonResponse
    {
        try {
            // Valida se o usuário pode dar 'index' em 'produtos'
            $this->produtoService->validarAcesso('index');
            
            $produtos = $this->produtoService->listarTodos();
            return $this->ReturnJson($produtos, 'Produtos listados com sucesso.', true, 200);
        } catch (Exception $e) {
            return $this->ReturnJson(null, $e->getMessage(), false, 403);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Valida se o usuário pode dar 'store' em 'produtos'
            $this->produtoService->validarAcesso('store');

            $formRequest = new StoreProdutoRequest();
            $validator = Validator::make($request->all(), $formRequest->rules(), $formRequest->messages());

            if ($validator->fails()) {
                return $this->ReturnJson($validator->errors(), 'Erro de validação.', false, 422);
            }

            $produto = $this->produtoService->criar($request->all());
            return $this->ReturnJson($produto, 'Produto cadastrado com sucesso.', true, 201);

        } catch (Exception $e) {
            return $this->ReturnJson(null, $e->getMessage(), false, 403);
        }
    }
}
