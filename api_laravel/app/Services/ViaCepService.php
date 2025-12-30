<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Exception;

class ViaCepService
{
    public function consultarCep(string $cep): array
    {
        // 1. Garante que o CEP tenha apenas números
        $cepLimpo = preg_replace('/[^0-9]/', '', trim($cep));

        if (strlen($cepLimpo) !== 8) {
            throw new Exception('O CEP informado deve conter exatamente 8 dígitos.');
        }

        try {
            // 2. Montagem da URL conforme exigido pela API (com /ws/ e /json/)
            $url = "viacep.com.br/ws/{$cepLimpo}/json/";

            /** @var Response $response */
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->get($url);

            // 3. Se retornar 404 aqui, significa que a URL acima foi mal montada
            if ($response->failed()) {
                throw new Exception("Erro na API ViaCEP (Status: {$response->status()})");
            }

            $dados = $response->json();

            // 4. ViaCEP retorna 200 OK com erro:true para CEPs não encontrados
            if (isset($dados['erro']) && ($dados['erro'] === true || $dados['erro'] === 'true')) {
                throw new Exception('CEP não encontrado na base de dados.');
            }

            return [
                'cep'         => $dados['cep'] ?? $cepLimpo,
                'logradouro'  => $dados['logradouro'] ?? null,
                'numero' => null,
                'complemento' => null,
                'bairro'      => $dados['bairro'] ?? null,
                'cidade'      => $dados['localidade'] ?? null,
                'estado'      => $dados['uf'] ?? null,
            ];
        } catch (Exception $e) {
            throw new Exception('Falha na consulta: ' . $e->getMessage());
        }
    }
}
