<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = ['fornecedores_id', 'valor_total', 'observacao', 'status'];

    public function itens() {
        return $this->hasMany(PedidoItem::class, 'pedidos_id');
    }

    public function fornecedor() {
        return $this->belongsTo(Fornecedor::class, 'fornecedores_id');
    }
}
