<?php

namespace App\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Este método roda antes das rules().
     * Se o usuário não existir, ele lança a exceção que seuExceptionHandler já captura.
     */
    protected function prepareForValidation()
    {
        $id = $this->route('user') ?? $this->route('id');

        // Se o ID não existir no banco, dispara 404 imediatamente
        // O seu ApiExceptionHandler vai capturar isso e retornar seu JSON customizado
        if (!User::where('id', $id)->exists()) {
            throw new ModelNotFoundException("Usuário não encontrado.");
        }
    }

    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                // Agora o ignore só roda se o usuário existir (por causa do prepareForValidation)
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'status' => ['sometimes', Rule::in(['ativo', 'inativo'])],
            'tipo'   => ['sometimes', Rule::in(['admin', 'vendedor'])],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário.',
            'status.in'    => 'Status inválido. Escolha entre: ativo ou inativo.',
            'tipo.in'      => 'Tipo de usuário inválido. Escolha entre: admin ou vendedor.',
        ];
    }
}
