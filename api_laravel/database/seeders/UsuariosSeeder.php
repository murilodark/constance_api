<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuário baseado no seu JSON (Vendedor)
        User::updateOrCreate(
            ['email' => 'vendedor@constance.com'], // Chave de busca
            [
                'name'     => 'Vendedor Constance',
                'password' => Hash::make('123456'), 
                'status'   => 'ativo',
                'tipo'     => 'vendedor',
            ]
        );

        // Usuário Administrador (Necessário para testar as áreas restritas do ERP)
        User::updateOrCreate(
            ['email' => 'admin@constance.com'],
            [
                'name'     => 'Admin Constance',
                'password' => Hash::make('123456'),
                'status'   => 'ativo',
                'tipo'     => 'admin',
            ]
        );
    }
}
