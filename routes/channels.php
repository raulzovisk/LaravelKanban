<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Board;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Registra os canais de broadcast para WebSockets
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal do quadro - apenas usuários com acesso podem se conectar
Broadcast::channel('board.{boardId}', function ($user, $boardId) {
    $board = Board::find($boardId);
    
    if (!$board) {
        return false;
    }
    
    return $board->hasAccess($user) ? [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatar,
    ] : false;
});
