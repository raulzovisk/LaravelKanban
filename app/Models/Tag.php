<?php
// app/Models/Tag.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    // Relacionamentos

    /**
     * Tarefas com esta tag
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class)->withTimestamps();
    }
}
