<?php
// database/migrations/2024_01_01_000004_create_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('column_id')->constrained()->onDelete('cascade'); // Coluna associada
            $table->foreignId('board_id')->constrained()->onDelete('cascade'); // Quadro associado
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Criador da tarefa
            $table->string('title'); // Título da tarefa
            $table->text('description')->nullable(); // Descrição detalhada
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium'); // Prioridade
            $table->date('due_date')->nullable(); // Data de vencimento
            $table->integer('order')->default(0); // Ordem dentro da coluna
            $table->timestamp('completed_at')->nullable(); // Data de conclusão
            $table->timestamps();
            $table->softDeletes(); // Soft delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
