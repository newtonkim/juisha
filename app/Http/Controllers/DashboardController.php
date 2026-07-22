<?php

namespace App\Http\Controllers;

use App\Services\FocusStats;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, FocusStats $stats): Response
    {
        $user = $request->user();
        $today = now()->toDateString();
        $rank = ['high' => 0, 'medium' => 1, 'low' => 2];

        $tasks = $user->tasks()->with('category:id,name')
            ->where(fn ($q) => $q->whereDate('due_at', $today)->orWhereDate('created_at', $today))
            ->get()
            ->sortBy(fn ($t) => [$t->isCompleted() ? 1 : 0, $rank[$t->priority]])
            ->values()
            ->map(fn ($t) => [
                'id' => $t->id, 'title' => $t->title, 'description' => $t->description,
                'priority' => $t->priority, 'status' => $t->status,
                'session_minutes' => $t->session_minutes, 'due_at' => $t->due_at?->toIso8601String(),
                'category' => $t->category?->only(['id', 'name']),
                'notify_on_end' => $t->notify_on_end, 'five_minute_warning' => $t->five_minute_warning,
            ]);

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'categories' => $user->categories()->orderBy('name')->get(['id', 'name']),
            'stats' => $stats->dailyProgress($user),
            'focus_minutes' => $user->getOrCreatePreferences()->focus_minutes,
        ]);
    }
}
