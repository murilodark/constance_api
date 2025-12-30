<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table = 'pedidos_itens';
    protected $fillable = ['pedidos_id', 'produtos_id', 'quantidade', 'valor_unitario'];

    public function produto() {
        return $this->belongsTo(Produto::class, 'produtos_id');
    }
}
