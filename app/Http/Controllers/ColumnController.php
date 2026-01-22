<?php
// app/Http/Controllers/ColumnController.php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Board;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\ColumnUpdated;

class ColumnController extends Controller
{
    /**
     * Armazena nova coluna
     */
    public function store(Request $request, Board $board)
    {
        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para criar colunas.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'limit' => 'nullable|integer|min:1',
            'position' => 'nullable|integer|min:0',
        ]);

        $position = (int) ($validated['position'] ?? -1);

        if ($position >= 0) {
            // Inserir em posição específica
            // position é o "order" da coluna anterior (0 se for no início)
            $targetOrder = $position + 1;

            // Incrementa a ordem de todas as colunas que estão na frente
            $board->columns()
                ->where('order', '>=', $targetOrder)
                ->increment('order');

            $order = $targetOrder;
        } else {
            // Inserir no final
            $maxOrder = $board->columns()->max('order') ?? 0;
            $order = $maxOrder + 1;
        }

        $column = $board->columns()->create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#6B7280',
            'limit' => $validated['limit'] ?? null,
            'order' => $order,
        ]);

        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Column::class,
            'subject_id' => $column->id,
            'type' => 'column.created',
            'properties' => ['column_name' => $column->name],
        ]);

        broadcast(new ColumnUpdated($board))->toOthers();

        return response()->json([
            'success' => true,
            'column' => $column,
            'message' => 'Coluna criada com sucesso!'
        ]);
    }

    /**
     * Atualiza coluna
     */
    public function update(Request $request, Column $column)
    {
        $board = $column->board;

        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para editar esta coluna.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'limit' => 'nullable|integer|min:1',
        ]);

        $column->update($validated);

        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Column::class,
            'subject_id' => $column->id,
            'type' => 'column.updated',
            'properties' => ['column_name' => $column->name],
        ]);

        broadcast(new ColumnUpdated($board))->toOthers();

        return response()->json([
            'success' => true,
            'column' => $column,
            'message' => 'Coluna atualizada com sucesso!'
        ]);
    }

    /**
     * Remove coluna
     */
    public function destroy(Column $column)
    {
        $board = $column->board;

        if (!$board->canEdit(Auth::user())) {
            abort(403, 'Você não tem permissão para deletar esta coluna.');
        }

        // Verifica se há tarefas na coluna
        if ($column->tasks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível deletar uma coluna com tarefas. Mova ou delete as tarefas primeiro.'
            ], 422);
        }

        Activity::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'subject_type' => Column::class,
            'subject_id' => $column->id,
            'type' => 'column.deleted',
            'properties' => ['column_name' => $column->name],
        ]);

        $column->delete();

        broadcast(new ColumnUpdated($board))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Coluna deletada com sucesso!'
        ]);
    }

    /**
     * Reordena colunas
     */
    public function reorder(Request $request, Board $board)
    {
        if (!$board->canEdit(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'columns' => 'required|array',
            'columns.*.id' => 'required|exists:columns,id',
            'columns.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['columns'] as $columnData) {
            Column::where('id', $columnData['id'])->update(['order' => $columnData['order']]);
        }

        broadcast(new ColumnUpdated($board))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Ordem das colunas atualizada!'
        ]);
    }
}
