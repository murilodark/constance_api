<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FornecedorEndereco extends Model
{
    use HasFactory;

    // Nome exato da tabela no banco
    protected $table = 'fornecedores_enderecos';

    protected $fillable = [
        'cep',
        'estado',
        'cidade',
        'bairro',
        'logradouro',
        'numero',
        'complemento',
        'fornecedores_id',
    ];

    /**
     * Relacionamento: O endereço pertence a um fornecedor
     */
    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedores_id', 'id');
    }
}
