<?php
// app/Events/ColumnUpdated.php

namespace App\Events;

use App\Models\Board;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ColumnUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function broadcastOn(): Channel
    {
        return new PresenceChannel('board.' . $this->board->id);
    }

    public function broadcastAs(): string
    {
        return 'column.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'board_id' => $this->board->id,
            'columns' => $this->board->columns,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
