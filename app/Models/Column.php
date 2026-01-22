<?php
// app/Models/Column.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'name',
        'order',
        'color',
        'limit',
    ];

    protected $casts = [
        'order' => 'integer',
        'limit' => 'integer',
    ];

    // Relacionamentos

    /**
     * Quadro associado
     */
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Tarefas da coluna
     */
    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }

    // Métodos auxiliares

    /**
     * Verifica se a coluna atingiu o limite
     */
    public function isAtLimit(): bool
    {
        if (!$this->limit) {
            return false;
        }

        return $this->tasks()->count() >= $this->limit;
    }

    /**
     * Conta tarefas na coluna
     */
    public function tasksCount(): int
    {
        return $this->tasks()->count();
    }
}
