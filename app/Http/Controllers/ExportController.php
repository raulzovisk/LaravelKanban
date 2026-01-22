<?php
// app/Http/Controllers/ExportController.php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Exporta tarefas para Excel
     */
    public function exportExcel(Board $board)
    {
        if (!$board->hasAccess(Auth::user())) {
            abort(403, 'Você não tem acesso a este quadro.');
        }

        return Excel::download(
            new TasksExport($board),
            "tarefas_{$board->name}_" . now()->format('Y-m-d') . ".xlsx"
        );
    }

    /**
     * Exporta tarefas para CSV
     */
    public function exportCsv(Board $board)
    {
        if (!$board->hasAccess(Auth::user())) {
            abort(403);
        }

        return Excel::download(
            new TasksExport($board),
            "tarefas_{$board->name}_" . now()->format('Y-m-d') . ".csv",
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Exporta tarefas para PDF
     */
    public function exportPdf(Board $board)
    {
        if (!$board->hasAccess(Auth::user())) {
            abort(403);
        }

        $board->load(['columns.tasks.tags', 'columns.tasks.user']);

        $pdf = Pdf::loadView('exports.tasks-pdf', compact('board'));

        return $pdf->download("tarefas_{$board->name}_" . now()->format('Y-m-d') . ".pdf");
    }
}
