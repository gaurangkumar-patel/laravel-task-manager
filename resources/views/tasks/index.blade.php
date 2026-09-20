@extends('layouts.app')

@section('title', 'Task Manager')

@section('content')
<div class="header-row">
    <div>
        <p class="eyebrow">Laravel 11</p>
        <h1>Task Manager</h1>
        <p class="muted">Create, edit, delete and reorder tasks by dragging them.</p>
    </div>
</div>

@if (session('status'))
    <div class="alert success">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid two-columns">
    <section class="card">
        <h2>Project</h2>

        @if ($projects->isNotEmpty())
            <form method="GET" action="{{ route('tasks.index') }}" class="stack compact">
                <label for="project">View tasks for</label>
                <select id="project" name="project" onchange="this.form.submit()">
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected($project->id === $selectedProjectId)>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        @else
            <p class="muted">Create a project before adding tasks.</p>
        @endif

        <form method="POST" action="{{ route('projects.store') }}" class="stack top-gap">
            @csrf
            <label for="project_name">Add a project</label>
            <div class="inline-form">
                <input id="project_name" name="name" type="text" maxlength="100" placeholder="e.g. Client work" required>
                <button type="submit" class="button secondary">Add</button>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>Add task</h2>
        <form method="POST" action="{{ route('tasks.store') }}" class="stack">
            @csrf

            <label for="name">Task name</label>
            <input id="name" name="name" type="text" maxlength="255" value="{{ old('name') }}" placeholder="What needs to be done?" required>

            <label for="project_id">Project</label>
            <select id="project_id" name="project_id" required @disabled($projects->isEmpty())>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected((int) old('project_id', $selectedProjectId) === $project->id)>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="button" @disabled($projects->isEmpty())>Create task</button>
        </form>
    </section>
</div>

<section class="card top-gap">
    <div class="section-heading">
        <div>
            <h2>Tasks</h2>
            <p class="muted small">Drag a task to change its priority. #1 is always the highest priority.</p>
        </div>
    </div>

    @if ($selectedProjectId && $tasks->isNotEmpty())
        <div
            id="task-list"
            class="task-list"
            data-project-id="{{ $selectedProjectId }}"
            data-reorder-url="{{ route('tasks.reorder') }}"
        >
            @foreach ($tasks as $task)
                <article class="task-item" draggable="true" data-task-id="{{ $task->id }}">
                    <div class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder">⋮⋮</div>
                    <span class="priority-badge">#{{ $task->priority }}</span>
                    <div class="task-content">
                        <strong>{{ $task->name }}</strong>
                        <span class="muted small">Updated {{ $task->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="task-actions">
                        <a class="button link-button" href="{{ route('tasks.edit', $task) }}">Edit</a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                            @csrf
                            @method('DELETE')
                            <button class="button danger" type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        <p id="save-status" class="muted small save-status" aria-live="polite"></p>
    @else
        <div class="empty-state">No tasks in this project yet.</div>
    @endif
</section>
@endsection
