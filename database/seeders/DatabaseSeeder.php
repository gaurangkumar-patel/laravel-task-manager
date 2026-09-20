<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Work', 'Personal', 'Learning'] as $projectName) {
            Project::firstOrCreate(['name' => $projectName]);
        }
    }
}
