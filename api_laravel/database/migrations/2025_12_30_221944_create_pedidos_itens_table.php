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
        Schema::create('pedidos_itens', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('pedidos_id')->index('fk_pedidos_itens_pedidos1_idx');
            $table->bigInteger('produtos_id')->index('fk_pedidos_itens_produtos1_idx');
            $table->integer('quantidade');
            $table->decimal('valor_unitario', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_itens');
    }
};
