<?php
// app/Models/Comment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'content',
    ];

    // Relacionamentos

    /**
     * Tarefa comentada
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Autor do comentário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
