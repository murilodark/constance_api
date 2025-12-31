<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'tipo',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * casts via método para mais flexibilidade
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'string',
            'tipo'              => 'string',
        ];
    }

    // --- SCOPES (Otimizados) ---

    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeAdmin($query)
    {
        return $query->where('tipo', 'admin');
    }

    public function scopeVendedor($query)
    {
        return $query->where('tipo', 'vendedor');
    }

    // --- RELACIONAMENTOS ---
    /**
     * Retorna todos os pedidos vinculados ao usuário (vendedor)
     */
    public function pedidos()
    {
        // Ajuste o nome da chave estrangeira conforme seu banco (ex: 'users_id')
        return $this->hasMany(Pedido::class, 'users_id');
    }

    public function fornecedores()
    {
        return $this->belongsToMany(Fornecedor::class, 'users_has_fornecedores', 'users_id', 'fornecedores_id')
            ->withTimestamps();
    }


    // --- MELHORIAS ADICIONAIS ---

    /**
     * Accessor para garantir que o nome sempre seja retornado com a primeira letra maiúscula
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucwords($value),
            set: fn(string $value) => strtolower($value),
        );
    }

    /**
     * Helper para verificar rapidamente se é admin
     */
    public function isAdmin(): bool
    {
        return $this->tipo === 'admin';
    }

    /**
     * Helper para verificar se está ativo
     */
    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }
}
