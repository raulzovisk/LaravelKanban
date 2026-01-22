<?php
// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'column_id',
        'board_id',
        'user_id',
        'title',
        'description',
        'priority',
        'due_date',
        'order',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'order' => 'integer',
    ];

    // Relacionamentos

    /**
     * Coluna da tarefa
     */
    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    /**
     * Quadro da tarefa
     */
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Criador da tarefa
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tags da tarefa
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Comentários da tarefa
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Atividades relacionadas
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    // Métodos auxiliares

    /**
     * Verifica se a tarefa está atrasada
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !$this->completed_at;
    }

    /**
     * Verifica se a tarefa está concluída
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Marca como concluída
     */
    public function markAsCompleted(): void
    {
        $this->update(['completed_at' => now()]);
    }

    /**
     * Marca como não concluída
     */
    public function markAsIncomplete(): void
    {
        $this->update(['completed_at' => null]);
    }

    /**
     * Retorna cor da prioridade
     */
    public function priorityColor(): string
    {
        return match($this->priority) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
