<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fornecedor extends Model
{
    use HasFactory;

    // Nome exato da tabela no banco
    protected $table = 'fornecedores';

    protected $fillable = [
        'nome',
        'cnpj',
        'status',
    ];

    /**
     * Relacionamento: Um fornecedor tem muitos endereços
     */
    public function enderecos(): HasMany
    {
        // Especificamos a FK 'fornecedores_id' que você definiu no SQL
        return $this->hasMany(FornecedorEndereco::class, 'fornecedores_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }
}
