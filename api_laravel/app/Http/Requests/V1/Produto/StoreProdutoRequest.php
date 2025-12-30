<?php

namespace App\Http\Requests\V1\Produto;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'referencia'      => ['required', 'string', 'max:100'],
            'nome'            => ['required', 'string', 'max:255'],
            'cor'             => ['nullable', 'string', 'max:100'],
            'preco'           => ['required', 'numeric', 'min:0'],
            'fornecedores_id' => ['required', 'integer', 'exists:fornecedores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'referencia.required'      => 'A referência do produto é obrigatória.',
            'nome.required'            => 'O nome do produto é obrigatório.',
            'preco.required'           => 'O preço é obrigatório.',
            'fornecedores_id.exists'   => 'O fornecedor informado não existe.',
        ];
    }
}
