<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Tarefas - {{ $board->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
        }

        h2 {
            color: #4F46E5;
            margin-top: 20px;
        }

        .task {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .task-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .task-meta {
            color: #666;
            font-size: 11px;
            margin-top: 5px;
        }

        .priority {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .priority-low {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .priority-medium {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .priority-high {
            background-color: #FED7AA;
            color: #9A3412;
        }

        .priority-urgent {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .tag {
            display: inline-block;
            padding: 2px 6px;
            margin-right: 5px;
            border-radius: 3px;
            font-size: 10px;
            background-color: #E0E7FF;
            color: #3730A3;
        }
    </style>
</head>

<body>
    <h1>📋 {{ $board->name }}</h1>
    <p><strong>Descrição:</strong> {{ $board->description }}</p>
    <p><strong>Data de Exportação:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    @foreach ($board->columns as $column)
        <h2>{{ $column->name }} ({{ $column->tasks->count() }} tarefas)</h2>

        @forelse($column->tasks as $task)
            <div class="task">
                <div class="task-title">
                    {{ $task->title }}
                    <span class="priority priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                </div>

                @if ($task->description)
                    <p>{{ $task->description }}</p>
                @endif

                @if ($task->tags->count() > 0)
                    <div>
                        @foreach ($task->tags as $tag)
                            <span class="tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="task-meta">
                    <strong>Criado por:</strong> {{ $task->user->name }} |
                    @if ($task->due_date)
                        <strong>Vencimento:</strong> {{ $task->due_date->format('d/m/Y') }} |
                    @endif
                    <strong>Criado em:</strong> {{ $task->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        @empty
            <p>Nenhuma tarefa nesta coluna.</p>
        @endforelse
    @endforeach
</body>

</html>
