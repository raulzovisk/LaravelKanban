<?php
// app/Events/BoardUpdated.php

namespace App\Events;

use App\Models\Board;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * Canal de broadcast
     */
    public function broadcastOn(): Channel
    {
        return new PresenceChannel('board.' . $this->board->id);
    }

    /**
     * Nome do evento
     */
    public function broadcastAs(): string
    {
        return 'board.updated';
    }

    /**
     * Dados para broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'board' => $this->board->load('columns'),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
