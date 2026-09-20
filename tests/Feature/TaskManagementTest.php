<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_task_can_be_created(): void
    {
        $project = Project::create(['name' => 'Work']);

        $response = $this->post('/tasks', [
            'name' => 'Build the feature',
            'project_id' => $project->id,
        ]);

        $response->assertRedirect(route('tasks.index', ['project' => $project->id]));
        $this->assertDatabaseHas('tasks', [
            'name' => 'Build the feature',
            'project_id' => $project->id,
            'priority' => 1,
        ]);
    }

    public function test_a_task_can_be_edited_and_moved_to_another_project(): void
    {
        $work = Project::create(['name' => 'Work']);
        $personal = Project::create(['name' => 'Personal']);
        $task = Task::create([
            'project_id' => $work->id,
            'name' => 'Old name',
            'priority' => 1,
        ]);

        $this->put("/tasks/{$task->id}", [
            'name' => 'Updated name',
            'project_id' => $personal->id,
        ])->assertRedirect(route('tasks.index', ['project' => $personal->id]));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated name',
            'project_id' => $personal->id,
            'priority' => 1,
        ]);
    }

    public function test_a_task_can_be_deleted_and_priorities_are_normalised(): void
    {
        $project = Project::create(['name' => 'Work']);
        $first = Task::create(['project_id' => $project->id, 'name' => 'First', 'priority' => 1]);
        $second = Task::create(['project_id' => $project->id, 'name' => 'Second', 'priority' => 2]);

        $this->delete("/tasks/{$first->id}")
            ->assertRedirect(route('tasks.index', ['project' => $project->id]));

        $this->assertDatabaseMissing('tasks', ['id' => $first->id]);
        $this->assertDatabaseHas('tasks', ['id' => $second->id, 'priority' => 1]);
    }

    public function test_tasks_can_be_reordered(): void
    {
        $project = Project::create(['name' => 'Work']);
        $first = Task::create(['project_id' => $project->id, 'name' => 'First', 'priority' => 1]);
        $second = Task::create(['project_id' => $project->id, 'name' => 'Second', 'priority' => 2]);
        $third = Task::create(['project_id' => $project->id, 'name' => 'Third', 'priority' => 3]);

        $this->postJson('/tasks/reorder', [
            'project_id' => $project->id,
            'order' => [$third->id, $first->id, $second->id],
        ])->assertOk();

        $this->assertDatabaseHas('tasks', ['id' => $third->id, 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $first->id, 'priority' => 2]);
        $this->assertDatabaseHas('tasks', ['id' => $second->id, 'priority' => 3]);
    }

    public function test_project_filter_only_shows_tasks_for_the_selected_project(): void
    {
        $work = Project::create(['name' => 'Work']);
        $personal = Project::create(['name' => 'Personal']);
        Task::create(['project_id' => $work->id, 'name' => 'Work task', 'priority' => 1]);
        Task::create(['project_id' => $personal->id, 'name' => 'Personal task', 'priority' => 1]);

        $this->get('/tasks?project='.$work->id)
            ->assertOk()
            ->assertSee('Work task')
            ->assertDontSee('Personal task');
    }
}
