<?php
// app/Notifications/BoardInvitation.php

namespace App\Notifications;

use App\Models\Board;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BoardInvitation extends Notification implements ShouldBroadcast
{
    public $board;
    public $inviter;

    public function __construct(Board $board, User $inviter)
    {
        $this->board = $board;
        $this->inviter = $inviter;
    }

    /**
     * Canais de notificação
     */
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Dados para database
     */
    public function toArray($notifiable): array
    {
        return [
            'board_id' => $this->board->id,
            'board_name' => $this->board->name,
            'inviter_id' => $this->inviter->id,
            'inviter_name' => $this->inviter->name,
            'message' => $this->inviter->name . ' convidou você para o quadro "' . $this->board->name . '"',
        ];
    }

    /**
     * Dados para broadcast (tempo real)
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id ?? uniqid(),
            'board_id' => $this->board->id,
            'board_name' => $this->board->name,
            'inviter_id' => $this->inviter->id,
            'inviter_name' => $this->inviter->name,
            'message' => $this->inviter->name . ' convidou você para o quadro "' . $this->board->name . '"',
            'created_at' => now()->toIso8601String(),
        ]);
    }
}
