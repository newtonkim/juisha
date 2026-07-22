<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FocusController extends Controller
{
    public function show(Request $request, Task $task): Response
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->load('category:id,name');
        $active = $task->sessions()->whereNull('ended_at')->latest('started_at')->first();
        $preferences = $request->user()->getOrCreatePreferences();

        return Inertia::render('Focus', [
            'task' => [
                'id' => $task->id, 'title' => $task->title, 'description' => $task->description,
                'priority' => $task->priority, 'status' => $task->status,
                'session_minutes' => $task->session_minutes, 'due_at' => $task->due_at?->toIso8601String(),
                'category' => $task->category?->only(['id', 'name']),
                'notify_on_end' => $task->notify_on_end, 'five_minute_warning' => $task->five_minute_warning,
            ],
            'active_session' => $active ? [
                'id' => $active->id,
                'started_at' => $active->started_at->toIso8601String(),
                'planned_seconds' => $task->session_minutes * 60,
            ] : null,
            'preferences' => [
                'browser_notifications' => $preferences->browser_notifications,
                'zen_mode' => $preferences->zen_mode,
                'focus_tone' => $preferences->focus_tone,
                'break_tone' => $preferences->break_tone,
            ],
        ]);
    }
}
