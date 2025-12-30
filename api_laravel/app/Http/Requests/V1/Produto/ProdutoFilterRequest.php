<?php

namespace App\Http\Requests\V1\Produto;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter'   => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page'     => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'O campo itens por página deve ser um número inteiro.',
            'page.integer'     => 'O campo página deve ser um número inteiro.',
        ];
    }
}
