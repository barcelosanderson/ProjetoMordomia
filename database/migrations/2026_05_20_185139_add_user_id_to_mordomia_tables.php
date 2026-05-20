<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona o campo user_id nas tabelas principais do sistema.
     *
     * O user_id identifica a qual usuário cada registro pertence.
     * Usei nullable para evitar erro caso já existam dados antigos no banco.
     * A partir dos controllers, todo novo registro será criado com user_id.
     */
    public function up(): void
    {
        Schema::table('tarefas', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
        });

        Schema::table('compromissos', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
        });

        Schema::table('transacoes', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Remove o user_id caso a migration seja revertida.
     */
    public function down(): void
    {
        Schema::table('tarefas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('compromissos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('transacoes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
