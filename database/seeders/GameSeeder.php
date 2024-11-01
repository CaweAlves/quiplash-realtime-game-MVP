<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        Game::factory()->waiting()->count(5)->create();
        Game::factory()->inProgress()->count(3)->create();
        Game::factory()->completed()->count(2)->create();
    }
}