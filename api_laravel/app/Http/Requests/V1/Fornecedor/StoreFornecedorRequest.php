<?php

namespace App\Http\Requests\V1\Fornecedor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFornecedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'   => ['required', 'string', 'min:3', 'max:255'],
            'cnpj'   => ['required', 'string', 'size:14', 'unique:fornecedores,cnpj'],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do fornecedor é obrigatório.',
            'nome.min'      => 'O nome deve ter pelo menos 3 caracteres.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.size'     => 'O CNPJ deve conter exatamente 14 dígitos (apenas números).',
            'cnpj.unique'   => 'Este CNPJ já está cadastrado para outro fornecedor.',
            'status.required'=> 'O campo status é obrigatório.',
            'status.in'     => 'O status selecionado é inválido. Escolha entre: ativo ou inativo.',
        ];
    }
}
