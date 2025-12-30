<?php

namespace App\Http\Requests\V1\Fornecedor;

use Illuminate\Foundation\Http\FormRequest;

class FornecedorFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter'   => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'     => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'A quantidade de itens por página deve ser um número.',
            'page.integer'     => 'O número da página deve ser um número válido.',
        ];
    }
}
