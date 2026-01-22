<?php
// database/migrations/2024_01_01_000003_create_columns_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade'); // Quadro associado
            $table->string('name'); // Nome da coluna (ex: "A Fazer")
            $table->integer('order')->default(0); // Ordem de exibição
            $table->string('color')->default('#6B7280'); // Cor da coluna
            $table->integer('limit')->nullable(); // Limite de tarefas (WIP limit)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('columns');
    }
};
