<?php
// database/seeders/BoardSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        // Cria quadro de exemplo
        $board = Board::create([
            'user_id' => $admin->id,
            'name' => 'Projeto Exemplo',
            'description' => 'Quadro de demonstração do sistema',
            'color' => '#3B82F6',
        ]);

        // Cria colunas
        $columns = [
            ['name' => 'Backlog', 'order' => 1, 'color' => '#6B7280'],
            ['name' => 'A Fazer', 'order' => 2, 'color' => '#EF4444'],
            ['name' => 'Em Progresso', 'order' => 3, 'color' => '#F59E0B'],
            ['name' => 'Revisão', 'order' => 4, 'color' => '#3B82F6'],
            ['name' => 'Concluído', 'order' => 5, 'color' => '#10B981'],
        ];

        foreach ($columns as $columnData) {
            $column = $board->columns()->create($columnData);

            // Cria algumas tarefas em cada coluna
            for ($i = 1; $i <= 3; $i++) {
                $task = Task::create([
                    'column_id' => $column->id,
                    'board_id' => $board->id,
                    'user_id' => $admin->id,
                    'title' => "Tarefa {$i} - {$column->name}",
                    'description' => "Descrição detalhada da tarefa {$i}",
                    'priority' => ['low', 'medium', 'high', 'urgent'][array_rand(['low', 'medium', 'high', 'urgent'])],
                    'due_date' => now()->addDays(rand(1, 30)),
                    'order' => $i,
                ]);

                // Adiciona tags aleatórias
                $task->tags()->attach(Tag::inRandomOrder()->limit(rand(1, 3))->pluck('id'));
            }
        }
    }
}
