<?php

namespace App\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class VinculoFornecedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Retornamos true aqui para permitir que a requisição chegue ao Controller.
        // A regra de negócio (Admin/Ativo) será validada no Service.
        return true;
    }

    public function rules(): array
    {
        return [
            'users_id'        => ['required', 'integer', 'exists:users,id'],
            'fornecedores_id' => ['required', 'integer', 'exists:fornecedores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'users_id.required'        => 'O ID do usuário é obrigatório.',
            'users_id.exists'          => 'O usuário informado não foi encontrado.',
            'fornecedores_id.required' => 'O ID do fornecedor é obrigatório.',
            'fornecedores_id.exists'   => 'O fornecedor informado não foi encontrado.',
        ];
    }
}
