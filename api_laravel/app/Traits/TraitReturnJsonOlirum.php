<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;


trait TraitReturnJsonOlirum
{
    /**
     * Retorna uma resposta JSON padronizada.
     *
     * @param mixed $data
     * @param string $message
     * @param bool $status
     * @param int $code
     * @return JsonResponse
     */
    public function ReturnJson($data = null, $message = '', $status = true, $code = 200): JsonResponse
    {
        // Garantir que o código HTTP esteja dentro de uma faixa válida (100-599)
        if ($code < 100 || $code > 599) {
            $code = 200;  // Default para 200 em caso de erro no código HTTP
        }

        // Garantir que o status seja um valor booleano
        $status = (bool) $status;

        // Retorna a resposta JSON
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
            'code' => $code
        ], $code, [], JSON_UNESCAPED_UNICODE);
    }
}