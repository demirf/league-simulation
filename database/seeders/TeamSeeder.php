<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Arsenal', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/9/92/Arsenal_Football_Club.png'],
            ['name' => 'Chelsea', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/hif/0/0d/Chelsea_FC.png'],
            ['name' => 'Manchester City', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/f/f6/Manchester_City.png'],
            ['name' => 'Liverpool', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/3/3f/150px-Liverpool_FC_logo.png'],
        ];

        Team::query()->insert($teams);
    }
}
