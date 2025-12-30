<?php

namespace App\Http\Requests\V1\Produto;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'referencia'      => ['sometimes', 'string', 'max:100'],
            'nome'            => ['sometimes', 'string', 'max:255'],
            'cor'             => ['nullable', 'string', 'max:100'],
            'preco'           => ['sometimes', 'numeric', 'min:0'],
            'fornecedores_id' => ['sometimes', 'integer', 'exists:fornecedores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'referencia.max'         => 'A referência não pode ultrapassar 100 caracteres.',
            'nome.max'               => 'O nome não pode ultrapassar 255 caracteres.',
            'preco.numeric'          => 'O preço deve ser um valor numérico.',
            'preco.min'              => 'O preço não pode ser negativo.',
            'fornecedores_id.exists' => 'O fornecedor informado não existe.',
        ];
    }
}
