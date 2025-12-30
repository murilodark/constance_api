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
        Schema::table('fornecedores_enderecos', function (Blueprint $table) {
            $table->foreign(['fornecedores_id'], 'fk_fornecedores_enderecos_fornecedores1')->references(['id'])->on('fornecedores')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fornecedores_enderecos', function (Blueprint $table) {
            $table->dropForeign('fk_fornecedores_enderecos_fornecedores1');
        });
    }
};
