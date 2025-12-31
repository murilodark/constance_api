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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('fornecedores_id')->index('fk_pedidos_fornecedores1_idx');
            $table->decimal('valor_total', 10)->nullable();
            $table->string('observacao')->nullable();
            $table->enum('status', ['Pendente', 'Concluído', 'Cancelado'])->nullable();
            $table->unsignedBigInteger('users_id')->index('fk_pedidos_users1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
