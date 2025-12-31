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
        Schema::table('users_has_fornecedores', function (Blueprint $table) {
            $table->foreign(['fornecedores_id'], 'fk_users_has_fornecedores_fornecedores1')->references(['id'])->on('fornecedores')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['users_id'], 'fk_users_has_fornecedores_users1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_has_fornecedores', function (Blueprint $table) {
            $table->dropForeign('fk_users_has_fornecedores_fornecedores1');
            $table->dropForeign('fk_users_has_fornecedores_users1');
        });
    }
};
