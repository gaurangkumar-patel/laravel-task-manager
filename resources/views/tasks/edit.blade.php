@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<section class="card narrow-card">
    <p class="eyebrow">Task #{{ $task->id }}</p>
    <h1>Edit task</h1>

    @if ($errors->any())
        <div class="alert error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.update', $task) }}" class="stack top-gap">
        @csrf
        @method('PUT')

        <label for="name">Task name</label>
        <input id="name" name="name" type="text" maxlength="255" value="{{ old('name', $task->name) }}" required>

        <label for="project_id">Project</label>
        <select id="project_id" name="project_id" required>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected((int) old('project_id', $task->project_id) === $project->id)>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>

        <div class="button-row">
            <button type="submit" class="button">Save changes</button>
            <a href="{{ route('tasks.index', ['project' => $task->project_id]) }}" class="button secondary link-button">Cancel</a>
        </div>
    </form>
</section>
@endsection
