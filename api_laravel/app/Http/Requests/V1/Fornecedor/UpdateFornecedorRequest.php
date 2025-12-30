<?php

namespace App\Http\Requests\V1\Fornecedor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Fornecedor;

class UpdateFornecedorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation()
    {
        // Captura o parâmetro {fornecedor} da rota
        $id = $this->route('fornecedor');

        // Se passar apenas o ID numérico ou o objeto, verificamos a existência
        $exists = is_numeric($id) 
            ? Fornecedor::where('id', $id)->exists() 
            : ($id instanceof Fornecedor);

        if (!$exists) {
            throw new ModelNotFoundException("Fornecedor não encontrado.");
        }
    }

    public function rules(): array
    {
        $id = $this->route('fornecedor');
        $fornecedorId = ($id instanceof Fornecedor) ? $id->id : $id;

        return [
            'nome'   => ['sometimes', 'string', 'min:3', 'max:255'],
            'cnpj'   => [
                'sometimes', 
                'string', 
                'size:14', 
                Rule::unique('fornecedores', 'cnpj')->ignore($fornecedorId)
            ],
            'status' => ['sometimes', Rule::in(['ativo', 'inativo'])],
            'endereco' => ['sometimes', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'cnpj.unique' => 'Este CNPJ já está cadastrado para outro fornecedor.',
            'status.in'    => 'Status inválido. Escolha entre: ativo ou inativo.',
        ];
    }
}
