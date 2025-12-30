<?php

namespace App\Http\Requests\V1\Pedido;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'     => ['sometimes', Rule::in(['Pendente', 'Concluído', 'Cancelado'])],
            'observacao' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'      => 'O status selecionado é inválido. Escolha entre: Pendente, Concluído ou Cancelado.',
            'observacao.max' => 'A observação não pode ultrapassar 255 caracteres.',
        ];
    }
}
