<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bpm;

class BPMSeeder extends Seeder
{
    // well wala paman ang employee's table so dli ko ka FK. Too Bad! - velvet underground 🍌
    public function run(): void
    {
        Bpm::factory()->count(10)->create();
    }
}
