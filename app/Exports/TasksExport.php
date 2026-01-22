<?php
// app/Exports/TasksExport.php

namespace App\Exports;

use App\Models\Board;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * Coleta os dados
     */
    public function collection()
    {
        return $this->board->tasks()
            ->with(['column', 'user', 'tags'])
            ->orderBy('column_id')
            ->orderBy('order')
            ->get();
    }

    /**
     * Define os cabeçalhos
     */
    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Descrição',
            'Coluna',
            'Prioridade',
            'Status',
            'Data de Vencimento',
            'Criado por',
            'Tags',
            'Data de Criação',
        ];
    }

    /**
     * Mapeia os dados
     */
    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->description,
            $task->column->name,
            ucfirst($task->priority),
            $task->isCompleted() ? 'Concluído' : 'Pendente',
            $task->due_date ? $task->due_date->format('d/m/Y') : '-',
            $task->user->name,
            $task->tags->pluck('name')->implode(', '),
            $task->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Estilos da planilha
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
