<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe dashboard com estatísticas
     */
    public function index()
    {
        $user = Auth::user();

        // Quadros acessíveis
        $boards = Board::where('user_id', $user->id)
            ->orWhereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->withCount('tasks')
            ->latest()
            ->get();

        // Estatísticas gerais
        $stats = [
            'total_boards' => $boards->count(),
            'total_tasks' => Task::whereIn('board_id', $boards->pluck('id'))->count(),
            'completed_tasks' => Task::whereIn('board_id', $boards->pluck('id'))
                ->whereNotNull('completed_at')->count(),
            'overdue_tasks' => Task::whereIn('board_id', $boards->pluck('id'))
                ->whereNull('completed_at')
                ->where('due_date', '<', now())
                ->count(),
        ];

        // Tarefas recentes do usuário
        $recentTasks = Task::where('user_id', $user->id)
            ->with(['board', 'column', 'tags'])
            ->latest()
            ->limit(5)
            ->get();

        // Tarefas por prioridade
        $tasksByPriority = Task::whereIn('board_id', $boards->pluck('id'))
            ->whereNull('completed_at')
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority');

        // Atividades recentes
        $recentActivities = $user->activities()
            ->with(['subject', 'board'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'boards',
            'stats',
            'recentTasks',
            'tasksByPriority',
            'recentActivities'
        ));
    }
}
