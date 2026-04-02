<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VampireGameSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('games')->updateOrInsert(
            ['slug' => 'vampire'],
            [
                'name' => 'Vampir Köylü',
                'description' => 'Gece vampirler avını seçer, gündüz köylüler linç için oy kullanır.',
                'min_players' => 4,
                'max_players' => 16,
                'settings' => json_encode(['roles' => ['vampir', 'koylu', 'doktor', 'dedektif', 'avci']]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
