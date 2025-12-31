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
        Schema::table('pedidos_itens', function (Blueprint $table) {
            $table->foreign(['pedidos_id'], 'fk_pedidos_itens_pedidos1')->references(['id'])->on('pedidos')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['produtos_id'], 'fk_pedidos_itens_produtos1')->references(['id'])->on('produtos')->onUpdate('no action')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos_itens', function (Blueprint $table) {
            $table->dropForeign('fk_pedidos_itens_pedidos1');
            $table->dropForeign('fk_pedidos_itens_produtos1');
        });
    }
};
