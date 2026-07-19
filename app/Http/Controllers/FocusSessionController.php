<?php

namespace App\Http\Controllers;

use App\Models\{FocusSession, Task};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\DB;

class FocusSessionController extends Controller
{
    public function store(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        DB::transaction(function () use ($request, $task) {
            $request->user()->focusSessions()->whereNull('ended_at')->lockForUpdate()->get()
                ->each(fn (FocusSession $open) => $open->update([
                    'ended_at' => now(),
                    'duration_seconds' => min($open->started_at->diffInSeconds(now()), $open->task->session_minutes * 60),
                    'completed' => false,
                ]));

            $task->sessions()->create(['user_id' => $request->user()->id, 'started_at' => now()]);
        });

        return redirect()->route('focus.show', $task);
    }

    public function finish(Request $request, FocusSession $session): RedirectResponse
    {
        abort_unless($session->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'elapsed_seconds' => ['required', 'integer', 'min:0'],
            'completed' => ['required', 'boolean'],
            'complete_task' => ['boolean'],
        ]);

        $session->update([
            'ended_at' => now(),
            'duration_seconds' => min($data['elapsed_seconds'], (int) $session->started_at->diffInSeconds(now())),
            'completed' => $data['completed'],
        ]);

        if ($request->boolean('complete_task')) {
            $session->task->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return redirect()->route('dashboard');
    }
}
