<?php
// database/migrations/2024_01_01_000010_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // Tipo de notificação
            $table->morphs('notifiable'); // Usuário que recebe
            $table->text('data'); // Dados da notificação
            $table->timestamp('read_at')->nullable(); // Se foi lida
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
