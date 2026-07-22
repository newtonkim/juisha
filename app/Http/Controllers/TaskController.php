<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(TaskRequest $request): RedirectResponse
    {
        $request->user()->tasks()->create($request->validated());

        return back();
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $this->ensureOwner($request, $task);
        $task->update($request->validated());

        return back();
    }

    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $this->ensureOwner($request, $task);
        $task->isCompleted()
            ? $task->update(['status' => 'pending', 'completed_at' => null])
            : $task->update(['status' => 'completed', 'completed_at' => now()]);

        return back();
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $this->ensureOwner($request, $task);
        $task->delete();

        return back();
    }

    private function ensureOwner(Request $request, Task $task): void
    {
        abort_unless($task->user_id === $request->user()->id, 403);
    }
}
