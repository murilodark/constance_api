<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\UserFilterRequest;
use App\Http\Requests\V1\User\StoreUserRequest;
use App\Http\Requests\V1\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Listar usuários
     */
    public function index(UserFilterRequest $request)
    {
        $filter = $request->input('filter');
        $perPage = $request->input('per_page', 30); // Número de itens por página, padrão é 10
        $currentPage = $request->input('page', 1); // Página atual, padrão é 1

        $query = User::query();

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'LIKE', "%{$filter}%")
                    ->orWhere('email', 'LIKE', "%{$filter}%");

                // Verifica se o filtro é um número para buscar pelo ID
                if (is_numeric($filter)) {
                    $q->orWhere('id', $filter);
                }
            });
        }

        // Aplica a ordenação e a paginação
        $list_users = $query->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $currentPage);
        return $this->ReturnJson($list_users, 'Listagem de usuários', true, 200);
    }

    /**
     * Criar usuário
     */
    public function store(StoreUserRequest $request)
    {

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => $request->status,
            'tipo'     => $request->tipo,
        ]);

        return $this->ReturnJson(
            $user,
            'Usuário criado com sucesso.',
            true,
            201
        );
    }

    /**
     * Exibir usuário
     */
    public function show(User $user)
    {
        return $this->ReturnJson(
            $user,
            'Usuário encontrado.'
        );
    }

    /**
     * Atualizar usuário
     */
    public function update(UpdateUserRequest $request, $user)
    {

        $validated = $request->validated();
        $user = User::findOrFail($user);
        $user->update($validated);
        return $this->returnJson($user, 'Sucesso!', true, 200);
    }

    /**
     * Remover usuário
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->ReturnJson(
            null,
            'Usuário removido com sucesso.'
        );
    }
}
