<?php
// database/seeders/TagSeeder.php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Bug', 'color' => '#EF4444'],
            ['name' => 'Feature', 'color' => '#3B82F6'],
            ['name' => 'Urgente', 'color' => '#DC2626'],
            ['name' => 'Documentação', 'color' => '#8B5CF6'],
            ['name' => 'Design', 'color' => '#EC4899'],
            ['name' => 'Backend', 'color' => '#10B981'],
            ['name' => 'Frontend', 'color' => '#F59E0B'],
            ['name' => 'Teste', 'color' => '#6366F1'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
