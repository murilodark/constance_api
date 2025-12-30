<?php

namespace App\Http\Requests\V1\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fornecedores_id'        => ['required', 'integer', 'exists:fornecedores,id'],
            'produtos'               => ['required', 'array', 'min:1'],
            'produtos.*.produtos_id' => ['required', 'integer', 'exists:produtos,id'],
            'produtos.*.quantidade'  => ['required', 'integer', 'min:1'],
            'observacao'             => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'fornecedores_id.required' => 'O fornecedor é obrigatório para iniciar um pedido.',
            'fornecedores_id.exists'   => 'O fornecedor informado não existe em nossa base de dados.',
            'produtos.required'        => 'É necessário adicionar pelo menos um produto ao pedido.',
            'produtos.array'           => 'O formato da lista de produtos é inválido.',
            'produtos.min'             => 'O pedido deve conter no mínimo 1 produto.',
            'produtos.*.produtos_id.required' => 'O ID do produto é obrigatório.',
            'produtos.*.produtos_id.exists'   => 'Um ou mais produtos selecionados não existem.',
            'produtos.*.quantidade.required'  => 'A quantidade é obrigatória para todos os itens.',
            'produtos.*.quantidade.min'       => 'A quantidade mínima de cada produto deve ser 1.',
            'observacao.max'           => 'A observação não pode ultrapassar 255 caracteres.',
        ];
    }
}
