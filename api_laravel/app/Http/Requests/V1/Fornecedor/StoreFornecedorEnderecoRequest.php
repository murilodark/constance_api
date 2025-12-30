<?php

namespace App\Http\Requests\V1\Fornecedor;

use Illuminate\Foundation\Http\FormRequest;

class StoreFornecedorEnderecoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cep'         => ['required', 'string', 'size:8'],
            'estado'      => ['nullable', 'string', 'size:2'],
            'cidade'      => ['nullable', 'string', 'max:255'],
            'bairro'      => ['nullable', 'string', 'max:255'],
            'logradouro'  => ['nullable', 'string', 'max:255'],
            'numero'      => ['nullable', 'string', 'max:45'],
            'complemento' => ['nullable', 'string', 'max:45'],
            'fornecedores_id' => ['required', 'exists:fornecedores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'cep.required'        => 'O CEP é obrigatório para a consulta.',
            'cep.size'            => 'O CEP deve conter exatamente 8 dígitos.',
            'estado.size'         => 'O estado deve conter apenas a UF (2 letras).',
            'fornecedores_id.required' => 'O endereço deve estar vinculado a um fornecedor.',
            'fornecedores_id.exists'   => 'O fornecedor informado não existe em nossa base.',
            // Campos de texto seguem o limite do seu SQL
            'cidade.max'          => 'A cidade excede o limite de caracteres.',
            'logradouro.max'      => 'O logradouro excede o limite de caracteres.',
        ];
    }
}
