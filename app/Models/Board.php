<?php
// app/Models/Board.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'color',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Relacionamentos

    /**
     * Dono do quadro
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuários com acesso ao quadro
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'invited_at')
            ->withTimestamps();
    }

    /**
     * Colunas do quadro
     */
    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    /**
     * Tarefas do quadro
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Atividades do quadro
     */
    public function activities()
    {
        return $this->hasMany(Activity::class)->latest();
    }

    // Métodos auxiliares

    /**
     * Verifica se o usuário tem acesso ao quadro
     */
    public function hasAccess(User $user): bool
    {
        return $this->user_id === $user->id || 
               $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Verifica se o usuário pode editar o quadro
     */
    public function canEdit(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        $pivot = $this->users()->where('user_id', $user->id)->first();
        return $pivot && in_array($pivot->pivot->role, ['editor', 'admin']);
    }

    /**
     * Verifica se o usuário é admin do quadro
     */
    public function isAdmin(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        $pivot = $this->users()->where('user_id', $user->id)->first();
        return $pivot && $pivot->pivot->role === 'admin';
    }
}
