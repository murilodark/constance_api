<?php

namespace App\Http\Requests\V1\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class PedidoFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'   => ['nullable', 'string'],
            'data_ini' => ['nullable', 'date_format:Y-m-d'],
            'data_fim' => ['nullable', 'date_format:Y-m-d'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'data_ini.date_format' => 'A data inicial deve estar no formato Ano-Mês-Dia (Ex: 2025-12-30).',
            'data_fim.date_format' => 'A data final deve estar no formato Ano-Mês-Dia (Ex: 2025-12-30).',
            'per_page.integer'     => 'A quantidade de itens por página deve ser um número.',
        ];
    }
}
