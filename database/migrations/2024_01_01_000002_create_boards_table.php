<?php
// database/migrations/2024_01_01_000002_create_boards_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Dono do quadro
            $table->string('name'); // Nome do quadro
            $table->text('description')->nullable(); // Descrição
            $table->string('color')->default('#3B82F6'); // Cor do quadro
            $table->boolean('is_public')->default(false); // Se é público
            $table->timestamps();
            $table->softDeletes(); // Soft delete para recuperação
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
