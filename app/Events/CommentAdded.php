<?php
// app/Events/CommentAdded.php

namespace App\Events;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $task;

    public function __construct(Comment $comment, Task $task)
    {
        $this->comment = $comment;
        $this->task = $task;
    }

    public function broadcastOn(): Channel
    {
        return new PresenceChannel('board.' . $this->task->board_id);
    }

    public function broadcastAs(): string
    {
        return 'comment.added';
    }

    public function broadcastWith(): array
    {
        return [
            'comment' => $this->comment,
            'task_id' => $this->task->id,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
