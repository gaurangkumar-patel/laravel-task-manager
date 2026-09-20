<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            'Work' => [
                'Review project requirements',
                'Fix API validation issue',
                'Prepare deployment checklist',
            ],
            'Personal' => [
                'Book dentist appointment',
                'Buy groceries',
                'Plan weekend activities',
            ],
            'Learning' => [
                'Watch Laravel course',
                'Practice database queries',
                'Read about queues and jobs',
            ],
        ];

        foreach ($projects as $projectName => $taskNames) {
            $project = Project::firstOrCreate(['name' => $projectName]);

            foreach ($taskNames as $taskName) {
                if ($project->tasks()->where('name', $taskName)->exists()) {
                    continue;
                }

                $project->tasks()->create([
                    'name' => $taskName,
                    'priority' => (int) $project->tasks()->max('priority') + 1,
                ]);
            }
        }
    }
}
