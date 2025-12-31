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
        Schema::create('users_has_fornecedores', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('users_id')->index('fk_users_has_fornecedores_users1_idx');
            $table->bigInteger('fornecedores_id')->index('fk_users_has_fornecedores_fornecedores1_idx');

            $table->unique(['users_id', 'fornecedores_id'], 'uq_users_fornecedores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_has_fornecedores');
    }
};
