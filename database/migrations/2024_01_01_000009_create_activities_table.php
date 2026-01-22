<?php
// database/migrations/2024_01_01_000009_create_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('subject'); // Polimórfico (pode ser task, column, etc)
            $table->string('type'); // Tipo de atividade (created, updated, moved, etc)
            $table->json('properties')->nullable(); // Dados adicionais em JSON
            $table->timestamps();
            
            // Índice para consultas rápidas
            $table->index(['board_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
