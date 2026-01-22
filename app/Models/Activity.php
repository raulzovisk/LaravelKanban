<?php
// app/Models/Activity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'user_id',
        'subject_type',
        'subject_id',
        'type',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // Relacionamentos

    /**
     * Quadro da atividade
     */
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Usuário que realizou a ação
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Objeto da atividade (polimórfico)
     */
    public function subject()
    {
        return $this->morphTo();
    }


    public function description(): string
    {
        return match ($this->type) {
            'task.created' => 'criou a tarefa',
            'task.updated' => 'atualizou a tarefa',
            'task.moved' => 'moveu a tarefa',
            'task.completed' => 'completou a tarefa',
            'task.deleted' => 'deletou a tarefa',
            'comment.created' => 'comentou na tarefa',
            'column.created' => 'criou a coluna',
            'column.updated' => 'atualizou a coluna',
            'column.deleted' => 'deletou a coluna',
            'user.invited' => 'convidou um usuário',
            'user.removed' => 'removeu um usuário', 
            'user.role_updated' => 'alterou a permissão de um usuário', 
            'board.created' => 'criou o quadro',
            'board.updated' => 'atualizou o quadro',
            default => 'realizou uma ação',
        };
    }

}
