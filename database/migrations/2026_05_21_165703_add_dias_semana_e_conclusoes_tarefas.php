<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona os dias da semana em tarefas e cria a tabela de conclusões.
     *
     * dias_semana usa números:
     * 0 = domingo
     * 1 = segunda
     * 2 = terça
     * 3 = quarta
     * 4 = quinta
     * 5 = sexta
     * 6 = sábado
     */
    public function up(): void
    {
        Schema::table('tarefas', function (Blueprint $table) {
            $table->json('dias_semana')->nullable();
        });

        /*
         * Para tarefas já existentes não sumirem da tela,
         * elas passam a aparecer em todos os dias da semana.
         */
        DB::table('tarefas')
            ->whereNull('dias_semana')
            ->update([
                'dias_semana' => json_encode([0, 1, 2, 3, 4, 5, 6]),
            ]);

        Schema::create('tarefa_conclusoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('tarefa_id')
                ->constrained('tarefas')
                ->cascadeOnDelete();

            $table->date('data');

            $table->timestamps();

            /*
             * Impede que a mesma tarefa seja concluída duas vezes
             * no mesmo dia.
             */
            $table->unique(['tarefa_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefa_conclusoes');

        Schema::table('tarefas', function (Blueprint $table) {
            $table->dropColumn('dias_semana');
        });
    }
};
