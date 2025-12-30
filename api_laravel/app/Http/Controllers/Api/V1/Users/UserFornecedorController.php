<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\DesvinculoFornecedorRequest;
use App\Services\FornecedorVinculoService;
use App\Http\Requests\V1\User\VinculoFornecedorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use Exception;

class UserFornecedorController extends Controller
{
    protected $vinculoService;

    public function __construct(FornecedorVinculoService $vinculoService)
    {
        $this->vinculoService = $vinculoService;
    }

/**
 * Lista os fornecedores vinculados a um usuário específico
 * Rota: GET /api/v1/users/{user}/fornecedores
 */
public function index(int $userId): JsonResponse
{
    try {
        // 1. Validação de Autorização (Regra de Negócio no Service)
        $this->vinculoService->validarAcesso();

        // 2. Execução da Listagem através do Service
        $resultado = $this->vinculoService->listarFornecedoresDoUsuario($userId);

        // 3. Retorno utilizando a Trait padrão do projeto
        return $this->ReturnJson(
            $resultado['data'],
            $resultado['message'],
            true,
            200
        );

    } catch (Exception $e) {
        return $this->ReturnJson(
            null,
            $e->getMessage(),
            false,
            403
        );
    }
}



    /**
     * Vincula um vendedor a um fornecedor
     * Rota: POST /api/v1/users/{user}/fornecedores
     */
    public function vincular(Request $request, $userId): JsonResponse
    {
        try {
            // 1. Validação de Autorização (Regra de Negócio no Service)
            // O erro 403 tem prioridade sobre os erros de validação de campos
            $this->vinculoService->validarAcesso();

            // 2. Validação Manual dos Campos
            // Mesclamos o userId da URL com os dados do Body para validar tudo de uma vez
            $dataToValidate = array_merge($request->all(), ['users_id' => $userId]);

            $formRequest = new VinculoFornecedorRequest();
            $validator = Validator::make(
                $dataToValidate,
                $formRequest->rules(),
                $formRequest->messages()
            );

            if ($validator->fails()) {
                return $this->ReturnJson(
                    $validator->errors(),
                    'Erro de validação dos campos.',
                    false,
                    422
                );
            }

            // 3. Execução do Vínculo no Service
            $resultado = $this->vinculoService->vincularVendedorAoFornecedor(
                (int) $userId,
                (int) $request->fornecedores_id
            );

            // Caso o fornecedor já esteja vinculado (Regra de duplicidade)
            if (!$resultado['success']) {
                return $this->ReturnJson(
                    null,
                    $resultado['message'],
                    false,
                    400
                );
            }

            // 4. Retorno de Sucesso com os dados inseridos
            return $this->ReturnJson(
                $resultado['data'],
                $resultado['message'],
                true,
                201
            );
        } catch (Exception $e) {
            // Captura o erro de autorização ou exceções inesperadas
            return $this->ReturnJson(
                null,
                $e->getMessage(),
                false,
                403
            );
        }
    }


    /**
     * Desvincula um vendedor de um fornecedor
     * Rota: DELETE /api/v1/users/{user}/fornecedores
     */
    public function desvincular(Request $request, $userId): JsonResponse
    {
        try {
            // 1. Validação de Autorização (Regra de Negócio no Service)
            // O erro de permissão continua sendo a primeira checagem
            $this->vinculoService->validarAcesso();

            // 2. Validação Manual dos Campos
            // Injetamos o userId da URL para que o DesvinculoFornecedorRequest valide se ele existe
            $dataToValidate = array_merge($request->all(), ['users_id' => $userId]);

            $formRequest = new DesvinculoFornecedorRequest();
            $validator = Validator::make(
                $dataToValidate,
                $formRequest->rules(),
                $formRequest->messages()
            );

            if ($validator->fails()) {
                return $this->ReturnJson(
                    $validator->errors(),
                    'Erro de validação dos campos.',
                    false,
                    422
                );
            }

            // 3. Execução do Desvínculo no Service
            $resultado = $this->vinculoService->desvincularVendedorDoFornecedor(
                (int) $userId,
                (int) $request->fornecedores_id
            );

            if (!$resultado['success']) {
                return $this->ReturnJson(
                    null,
                    $resultado['message'],
                    false,
                    400
                );
            }

            // 4. Retorno de Sucesso (200 OK)
            return $this->ReturnJson(
                $resultado['data'],
                $resultado['message'],
                true,
                200
            );
        } catch (Exception $e) {
            // Captura erro 403 de autorização ou falhas críticas
            return $this->ReturnJson(
                null,
                $e->getMessage(),
                false,
                403
            );
        }
    }


    

}
