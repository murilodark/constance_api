<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermission
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        try {
            // Pega o nome do método do Controller (index, store, vincular, etc)
            $metodo = $request->route()->getActionMethod();

            // Chama o seu AuthService centralizado
            $this->authService->checkPermission($modulo, $metodo);

            return $next($request);
            
        } catch (\Exception $e) {
            // Retorna o erro no formato da sua Trait (assumindo que o middleware possa acessar ou emular a resposta)
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => false,
                'code' => 403
            ], 403);
        }
    }
}
