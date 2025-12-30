<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserHasFornecedor extends Pivot
{
    protected $table = 'users_has_fornecedores';

    // Definimos como false pois a tabela usa nomes específicos ou timestamps manuais
    public $timestamps = true;

    protected $fillable = [
        'users_id',
        'fornecedores_id',
        'updated_at'
    ];

    /**
     * Relacionamento com o Usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relacionamento com o Fornecedor
     */
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedores_id');
    }
}
