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
        Schema::create('fornecedores_enderecos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->char('cep', 8)->nullable();
            $table->char('estado', 2)->nullable();
            $table->string('cidade')->nullable();
            $table->string('bairro')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 45)->nullable();
            $table->string('complemento', 45)->nullable();
            $table->bigInteger('fornecedores_id')->index('fk_fornecedores_enderecos_fornecedores1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedores_enderecos');
    }
};
