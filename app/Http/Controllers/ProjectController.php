<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:projects,name'],
        ]);

        $project = Project::create($validated);

        return redirect()
            ->route('tasks.index', ['project' => $project->id])
            ->with('status', 'Project created.');
    }
}
