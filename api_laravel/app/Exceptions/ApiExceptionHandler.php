<?php

namespace App\Exceptions;

use App\Traits\TraitReturnJsonOlirum;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Exception;

class ApiExceptionHandler
{
    use TraitReturnJsonOlirum;

    public function __invoke(Exceptions $exceptions)
    {
        // 1. Forçar JSON para rotas API
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*'));

        // 2. Formatar Respostas
        $exceptions->render(function (Exception $e, Request $request) {
            if (!$request->is('api/*')) {
                return null; // Deixa o Laravel tratar rotas Web normalmente
            }

            return match (true) {
                $e instanceof ValidationException => 
                    $this->returnJson($e->errors(), 'Erro de validação dos campos.', false, 422),
                
                $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => 
                    $this->returnJson(null, 'Registro ou rota não encontrada.', false, 404),
                
                $e instanceof AuthenticationException => 
                    $this->returnJson(null, 'Não autenticado.', false, 401),
                
                $e instanceof MethodNotAllowedHttpException => 
                    $this->returnJson(null, 'Método HTTP não permitido.', false, 405),
                
                $e instanceof HttpExceptionInterface => 
                    $this->returnJson(null, $e->getMessage(), false, $e->getStatusCode()),

                default => $this->returnJson(
                    config('app.debug') ? $e->getMessage() : null, 
                    'Erro interno no servidor.', 
                    false, 
                    500
                ),
            };
        });
    }
}
