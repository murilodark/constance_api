<?php

namespace App\Http\Controllers\Api\V1\Utils;

use App\Http\Controllers\Controller;
use App\Services\ViaCepService;
use Illuminate\Http\Request;
use Exception;

class CepController extends Controller
{
    protected $viaCepService;

    public function __construct(ViaCepService $viaCepService)
    {
        $this->viaCepService = $viaCepService;
    }

    /**
     * Consulta CEP de forma global para a API
     */
    public function __invoke(Request $request, string $cep)
    {
        try {
            $dados = $this->viaCepService->consultarCep($cep);
            
            return $this->ReturnJson(
                $dados, 
                'CEP consultado com sucesso.'
            );
        } catch (Exception $e) {
            return $this->ReturnJson(
                null, 
                $e->getMessage(), 
                false, 
                400
            );
        }
    }
}
