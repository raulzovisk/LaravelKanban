<?php
// app/Events/TaskMoved.php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $oldColumnId;

    public function __construct(Task $task, $oldColumnId)
    {
        $this->task = $task;
        $this->oldColumnId = $oldColumnId;
    }

    public function broadcastOn(): Channel
    {
        return new PresenceChannel('board.' . $this->task->board_id);
    }

    public function broadcastAs(): string
    {
        return 'task.moved';
    }

    public function broadcastWith(): array
    {
        return [
            'task' => $this->task,
            'old_column_id' => $this->oldColumnId,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
