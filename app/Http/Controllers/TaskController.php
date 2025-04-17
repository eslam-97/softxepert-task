<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\SearchTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\TaskStatus;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{

    public function index(SearchTaskRequest $request)
    {
        $query = Task::query()->with('dependencies');

        if (!$request->user()->isManager()) {
            $query->where('assigned_user_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_user_id') && $request->user()->isManager()) {
            $query->where('assigned_user_id', $request->assigned_user_id);
        }

        if ($request->filled('due_date_start')) {
            $query->where('due_date', '>=', $request->due_date_start);
        }

        if ($request->filled('due_date_end')) {
            $query->where('due_date', '<=', $request->due_date_end);
        }

        return $query->get();
    }

    public function store(CreateTaskRequest $request)
    {
        Gate::authorize('create', Task::class);

        $validated = $request->validated();

        $task = Task::create($validated + ['created_by' => $request->user()->id]);

        $task->dependencies()->attach($validated['dependencies'] ?? []);

        $task->load('dependencies');

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        return $task->load('dependencies');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $user = $request->user();
        $validated = $request->validated();

        if (isset($validated['status']) && $validated['status'] === TaskStatus::COMPLETED->value) {

            if ($task->dependencies()->where('status', '!=', 'completed')->exists()) {
                throw new HttpResponseException(response()->json([
                    'message' => 'All dependencies must be completed.',
                    'incomplete_dependencies' => $task->dependencies()
                        ->where('status', '!=', 'completed')
                        ->pluck('title')
                ], 422));
            }
        }

        $task->update($validated);

        if ($user->isManager()) {
            $task->dependencies()->sync($validated['dependencies'] ?? []);
        }

        $task->load('dependencies');

        return response()->json($task, 200);
    }

    public function assignUser(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update(['assigned_user_id' => $request->assigned_user_id]);

        $task->load('dependencies');

        return response()->json($task, 200);
    }
}
