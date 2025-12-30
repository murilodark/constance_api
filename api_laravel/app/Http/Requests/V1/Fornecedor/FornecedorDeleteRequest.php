<?php

namespace App\Http\Requests\V1\Fornecedor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Fornecedor;

class FornecedorDeleteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation()
    {
        $id = $this->route('fornecedor');
        $exists = is_numeric($id) ? Fornecedor::where('id', $id)->exists() : ($id instanceof Fornecedor);

        if (!$exists) {
            throw new ModelNotFoundException("Fornecedor não encontrado.");
        }
    }

    public function rules(): array { return []; }
}
