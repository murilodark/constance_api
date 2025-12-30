<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produto extends Model
{
    protected $table = 'produtos';

    protected $fillable = [
        'referencia',
        'nome',
        'cor',
        'preco',
        'fornecedores_id'
    ];

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedores_id');
    }
}
