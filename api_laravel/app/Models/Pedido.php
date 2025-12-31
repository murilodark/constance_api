<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedidos';

    /**
     * users_id é obrigatório conforme seu CREATE TABLE (NOT NULL)
     */
    protected $fillable = [
        'fornecedores_id', 
        'users_id', 
        'valor_total', 
        'observacao', 
        'status'
    ];

    /**
     * Casts atualizados para 2025:
     * users_id e fornecedores_id como integer para evitar problemas de string no MySQL
     */
    protected $casts = [
        'users_id'        => 'integer',
        'fornecedores_id' => 'integer',
        'valor_total'     => 'decimal:2',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Relacionamento com o Vendedor (Usuário)
     * Referência: fk_pedidos_users1 -> users_id
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relacionamento com o Fornecedor
     * Referência: fk_pedidos_fornecedores1 -> fornecedores_id
     */
    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedores_id');
    }

    /**
     * Relacionamento com os Itens do Pedido
     */
    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'pedidos_id');
    }

    // --- SCOPES DE FILTRO ---

    public function scopePendente($query)
    {
        return $query->where('status', 'Pendente');
    }

    public function scopeConcluido($query)
    {
        return $query->where('status', 'Concluído');
    }

    public function scopeCancelado($query)
    {
        return $query->where('status', 'Cancelado');
    }
}
