<?php
// app/Http/Controllers/CommentController.php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\CommentAdded;
use App\Notifications\TaskCommented;

class CommentController extends Controller
{
    /**
     * Adiciona comentário à tarefa
     */
    public function store(Request $request, Task $task)
    {
        $board = $task->board;

        if (!$board->hasAccess(Auth::user())) {
            abort(403, 'Você não tem acesso a esta tarefa.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        // Registra atividade
        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'type' => 'comment.created',
            'properties' => [
                'task_title' => $task->title,
                'comment_preview' => substr($comment->content, 0, 50),
            ],
        ]);

        // Notifica o criador da tarefa (se não for ele mesmo)
        if ($task->user_id !== Auth::id()) {
            $task->user->notify(new TaskCommented($task, $comment));
        }

        // Broadcast
        broadcast(new CommentAdded($comment->load('user'), $task))->toOthers();

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'message' => 'Comentário adicionado com sucesso!'
        ]);
    }

    /**
     * Atualiza comentário
     */
    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Você não pode editar este comentário.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validated);

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Comentário atualizado com sucesso!'
        ]);
    }

    /**
     * Remove comentário
     */
    public function destroy(Comment $comment)
    {
        $task = $comment->task;
        $board = $task->board;

        // Pode deletar se for o autor ou admin do quadro
        if ($comment->user_id !== Auth::id() && !$board->isAdmin(Auth::user())) {
            abort(403, 'Você não pode deletar este comentário.');
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentário deletado com sucesso!'
        ]);
    }
}
