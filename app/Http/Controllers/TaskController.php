<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Board;
use App\Models\Column;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskMoved;

class TaskController extends Controller
{
    /**
     * Armazena nova tarefa
     */
    public function store(StoreTaskRequest $request)
    {
        $column = Column::findOrFail($request->column_id);
        $board = $column->board;

        // Verifica permissão
        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para criar tarefas neste quadro.');
        }

        // Obtém próxima ordem
        $maxOrder = $column->tasks()->max('order') ?? 0;

        $task = Task::create([
            'column_id' => $column->id,
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'order' => $maxOrder + 1,
        ]);

        // Adiciona tags se fornecidas
        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        }

        // Registra atividade
        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'type' => 'task.created',
            'properties' => ['task_title' => $task->title],
        ]);

        // Broadcast para tempo real
        broadcast(new TaskCreated($task->load(['column', 'tags', 'user'])))->toOthers();

        return response()->json([
            'success' => true,
            'task' => $task->load(['column', 'tags', 'user']),
            'message' => 'Tarefa criada com sucesso!'
        ]);
    }

    /**
     * Exibe detalhes da tarefa
     */
    public function show(Request $request, Task $task)
    {
        $board = $task->board;

        if (!$board->hasAccess(Auth::user())) {
            abort(403, 'Você não tem acesso a esta tarefa.');
        }

        $task->load(['column', 'tags', 'user', 'comments.user', 'activities.user']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        }

        return view('tasks.show', compact('task'));
    }

    /**
     * Atualiza tarefa
     */
    /**
     * Atualiza tarefa
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $board = $task->board;

        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para editar esta tarefa.');
        }

        // Verifica se é tarefa concluída e se usuário é admin
        $isCompletedColumn = false;
        if ($task->column && in_array(strtolower($task->column->name), ['concluído', 'concluido', 'done', 'completed', 'finalizado'])) {
            $isCompletedColumn = true;
        }

        if ($isCompletedColumn && !$board->isAdmin(Auth::user())) {
            abort(403, 'Apenas administradores podem editar tarefas concluídas.');
        }

        $oldData = $task->toArray();
        $task->update($request->validated());

        // Atualiza tags
        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        }

        // Registra atividade
        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'type' => 'task.updated',
            'properties' => [
                'task_title' => $task->title,
                'old_data' => $oldData,
                'new_data' => $task->toArray(),
            ],
        ]);

        // Broadcast
        broadcast(new TaskUpdated($task->load(['column', 'tags', 'user'])))->toOthers();

        return response()->json([
            'success' => true,
            'task' => $task->load(['column', 'tags', 'user']),
            'message' => 'Tarefa atualizada com sucesso!'
        ]);
    }

    /**
     * Move tarefa entre colunas (drag & drop)
     */
    public function move(Request $request, Task $task)
    {
        $board = $task->board;

        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para mover esta tarefa.');
        }

        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'order' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:1000', // Comentário opcional (obrigatório se concluir)
        ]);

        $newColumn = Column::findOrFail($validated['column_id']);

        // Verifica se a coluna pertence ao mesmo quadro
        if ($newColumn->board_id !== $board->id) {
            abort(400, 'Coluna inválida.');
        }

        // Verifica se está movendo PARA coluna de conclusão
        // TODO: Melhor seria ter uma flag 'is_completed' na tabela columns, mas faremos verificação por nome por enquanto
        $isCompleting = in_array(strtolower($newColumn->name), ['concluído', 'concluido', 'done', 'completed', 'finalizado']);

        // Se estiver concluindo, exige comentário
        if ($isCompleting && empty($validated['comment']) && $task->column_id != $newColumn->id) {
            return response()->json([
                'success' => false,
                'require_comment' => true,
                'message' => 'É necessário adicionar um comentário ao concluir a tarefa.'
            ], 422);
        }

        // Se a tarefa JÁ estava em concluída e está tentando sair ou mover, permitir?
        // Regra diz: "apenas o adm conseguirá altera-lo posteriormente". 
        // Assumindo que mover DE concluído para outra também é alterar.
        $wasCompleted = $task->column && in_array(strtolower($task->column->name), ['concluído', 'concluido', 'done', 'completed', 'finalizado']);

        if ($wasCompleted && $task->column_id != $newColumn->id && !$board->isAdmin(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas administradores podem reabrir ou mover tarefas concluídas.'
            ], 403);
        }

        $oldColumnId = $task->column_id;

        // Atualiza tarefa
        $updateData = [
            'column_id' => $validated['column_id'],
            'order' => $validated['order'],
        ];

        // Se concluiu, marca data
        if ($isCompleting) {
            $updateData['completed_at'] = now();
        } elseif ($wasCompleted && !$isCompleting) {
            // Se reabriu
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        // Salva comentário se houver
        if (!empty($validated['comment'])) {
            $task->comments()->create([
                'user_id' => Auth::id(),
                'content' => $validated['comment']
            ]);
        }

        // Reordena tarefas na coluna antiga
        Task::where('column_id', $oldColumnId)
            ->where('order', '>', $task->order)
            ->decrement('order');

        // Reordena tarefas na nova coluna
        Task::where('column_id', $validated['column_id'])
            ->where('id', '!=', $task->id)
            ->where('order', '>=', $validated['order'])
            ->increment('order');

        // Registra atividade
        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'type' => 'task.moved',
            'properties' => [
                'task_title' => $task->title,
                'from_column' => Column::find($oldColumnId)->name,
                'to_column' => $newColumn->name,
                'comment' => $validated['comment'] ?? null
            ],
        ]);

        // Broadcast
        broadcast(new TaskMoved($task->load(['column', 'tags', 'user']), $oldColumnId))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Tarefa movida com sucesso!'
        ]);
    }

    /**
     * Sincroniza ordem de múltiplas tarefas
     */
    public function syncOrder(Request $request)
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.column_id' => 'required|exists:columns,id',
            'tasks.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            Task::where('id', $taskData['id'])->update([
                'column_id' => $taskData['column_id'],
                'order' => $taskData['order'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }

    /**
     * Remove tarefa
     */
    public function destroy(Task $task)
    {
        $board = $task->board;

        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para deletar esta tarefa.');
        }

        // Registra atividade antes de deletar
        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'type' => 'task.deleted',
            'properties' => ['task_title' => $task->title],
        ]);

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarefa deletada com sucesso!'
        ]);
    }

    /**
     * Busca tarefas com filtros
     */
    public function search(Request $request, Board $board)
    {
        if (!$board->hasAccess(Auth::user())) {
            abort(403);
        }

        $query = $board->tasks()->with(['column', 'tags', 'user']);

        // Filtro por termo de busca
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por prioridade
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtro por tags
        if ($request->has('tags')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }

        // Filtro por status (concluído/não concluído)
        if ($request->has('completed')) {
            if ($request->completed === 'true') {
                $query->whereNotNull('completed_at');
            } else {
                $query->whereNull('completed_at');
            }
        }

        // Filtro por data de vencimento
        if ($request->has('overdue') && $request->overdue === 'true') {
            $query->whereNull('completed_at')
                ->where('due_date', '<', now());
        }

        $tasks = $query->get();

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }
}
