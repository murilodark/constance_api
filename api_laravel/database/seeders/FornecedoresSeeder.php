<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fornecedor;
use App\Models\Produto;

class FornecedoresSeeder extends Seeder
{
    public function run(): void
    {
        // Cria o Fornecedor que vimos no seu JSON
        $fornecedor = Fornecedor::create([
            'nome'   => 'Calçados Constance Oficial',
            'cnpj'   => '12345678000199',
            'status' => 'ativo',
        ]);

        // Cria Produtos vinculados a esse fornecedor
        Produto::create([
            'fornecedores_id' => $fornecedor->id,
            'referencia'      => 'CALC-CONST-030',
            'nome'            => 'Sapato Loafers Luxo',
            'cor'             => 'Preto',
            'preco'           => 265.00,
        ]);

        Produto::create([
            'fornecedores_id' => $fornecedor->id,
            'referencia'      => 'CALC-CONST-045',
            'nome'            => 'Scarpin Salto Alto',
            'cor'             => 'Nude',
            'preco'           => 189.90,
        ]);
    }
}
