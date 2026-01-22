<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'timezone',
        'dark_mode',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dark_mode' => 'boolean',
    ];

    // Relacionamentos

    /**
     * Quadros criados pelo usuário
     */
    public function ownedBoards()
    {
        return $this->hasMany(Board::class);
    }

    /**
     * Quadros compartilhados com o usuário
     */
    public function sharedBoards()
    {
        return $this->belongsToMany(Board::class)
            ->withPivot('role', 'invited_at')
            ->withTimestamps();
    }

    /**
     * Todos os quadros acessíveis (próprios + compartilhados)
     */
    public function accessibleBoards()
    {
        return Board::where('user_id', $this->id)
            ->orWhereHas('users', function ($query) {
                $query->where('user_id', $this->id);
            });
    }

    /**
     * Tarefas criadas pelo usuário
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Comentários do usuário
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Atividades do usuário
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Define o canal de broadcast para notificações privadas
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'App.Models.User.' . $this->id;
    }
}
