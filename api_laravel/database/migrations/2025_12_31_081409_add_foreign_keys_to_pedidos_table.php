<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreign(['fornecedores_id'], 'fk_pedidos_fornecedores1')->references(['id'])->on('fornecedores')->onUpdate('no action')->onDelete('restrict');
            $table->foreign(['users_id'], 'fk_pedidos_users1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign('fk_pedidos_fornecedores1');
            $table->dropForeign('fk_pedidos_users1');
        });
    }
};
