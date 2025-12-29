<?php

namespace App\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para criação de usuário.
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'status'   => ['required', Rule::in(['ativo', 'inativo'])],
            'tipo'     => ['required', Rule::in(['admin', 'vendedor'])],
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            // Nome
            'name.required'     => 'O campo nome é obrigatório.',
            'name.string'       => 'O nome deve ser um texto válido.',
            'name.min'          => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max'          => 'O nome não pode ultrapassar 255 caracteres.',

            // E-mail
            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'Informe um endereço de e-mail válido.',
            'email.max'         => 'O e-mail não pode ultrapassar 255 caracteres.',
            'email.unique'      => 'Este e-mail já está cadastrado em nossa base de dados.',

            // Senha
            'password.required' => 'O campo senha é obrigatório.',
            'password.min'      => 'A senha deve ter no mínimo 6 caracteres.',

            // Status
            'status.required'   => 'O campo status é obrigatório.',
            'status.in'         => 'O status selecionado é inválido. Escolha entre: ativo ou inativo.',

            // Tipo
            'tipo.required'     => 'O campo tipo é obrigatório.',
            'tipo.in'           => 'O tipo de usuário é inválido. Escolha entre: admin ou vendedor.',
        ];
    }
}
