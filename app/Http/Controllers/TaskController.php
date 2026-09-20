<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()->orderBy('name')->get();
        $selectedProjectId = $request->integer('project') ?: $projects->first()?->id;

        $tasks = Task::query()
            ->when($selectedProjectId, fn ($query) => $query->where('project_id', $selectedProjectId))
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('projects', 'selectedProjectId', 'tasks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        DB::transaction(function () use ($validated): void {
            $nextPriority = (int) Task::query()
                ->where('project_id', $validated['project_id'])
                ->max('priority') + 1;

            Task::create([
                ...$validated,
                'priority' => $nextPriority,
            ]);
        });

        return redirect()
            ->route('tasks.index', ['project' => $validated['project_id']])
            ->with('status', 'Task created.');
    }

    public function edit(Task $task): View
    {
        $projects = Project::query()->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        DB::transaction(function () use ($task, $validated): void {
            $oldProjectId = $task->project_id;
            $newProjectId = (int) $validated['project_id'];

            if ($oldProjectId !== $newProjectId) {
                $task->update([
                    'name' => $validated['name'],
                    'project_id' => $newProjectId,
                    'priority' => (int) Task::query()
                        ->where('project_id', $newProjectId)
                        ->max('priority') + 1,
                ]);

                $this->normalisePriorities($oldProjectId);
            } else {
                $task->update(['name' => $validated['name']]);
            }
        });

        return redirect()
            ->route('tasks.index', ['project' => $validated['project_id']])
            ->with('status', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;

        DB::transaction(function () use ($task, $projectId): void {
            $task->delete();
            $this->normalisePriorities($projectId);
        });

        return redirect()
            ->route('tasks.index', ['project' => $projectId])
            ->with('status', 'Task deleted.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'distinct'],
        ]);

        $projectId = (int) $validated['project_id'];
        $taskIds = array_map('intval', $validated['order']);

        $projectTaskIds = Task::query()
            ->where('project_id', $projectId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        $submittedTaskIds = collect($taskIds)->sort()->values()->all();

        abort_unless($projectTaskIds === $submittedTaskIds, 422, 'The task order is invalid.');

        DB::transaction(function () use ($taskIds, $projectId): void {
            foreach ($taskIds as $index => $taskId) {
                Task::query()
                    ->where('id', $taskId)
                    ->where('project_id', $projectId)
                    ->update(['priority' => $index + 1]);
            }
        });

        return response()->json(['message' => 'Task priorities updated.']);
    }

    private function normalisePriorities(int $projectId): void
    {
        Task::query()
            ->where('project_id', $projectId)
            ->orderBy('priority')
            ->orderBy('id')
            ->get()
            ->each(function (Task $task, int $index): void {
                $task->update(['priority' => $index + 1]);
            });
    }
}
