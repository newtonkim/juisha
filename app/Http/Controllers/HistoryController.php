<?php

namespace App\Http\Controllers;

use App\Services\FocusStats;
use Illuminate\Http\Request;
use Inertia\{Inertia, Response};

class HistoryController extends Controller
{
    public function index(Request $request, FocusStats $stats): Response
    {
        $user = $request->user();
        $weekly = $stats->weeklyCompleted($user);

        $rows = $user->tasks()->with('category:id,name')->withSum('sessions', 'duration_seconds')
            ->where('status', 'completed')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->string('priority')))
            ->when($request->filled('days'), fn ($q) => $q->where('completed_at', '>=', now()->subDays($request->integer('days'))))
            ->latest('completed_at')->limit(50)->get()
            ->map(fn ($t) => [
                'id' => $t->id, 'title' => $t->title, 'priority' => $t->priority,
                'category' => $t->category?->name, 'completed_at' => $t->completed_at?->toIso8601String(),
                'focused_seconds' => (int) ($t->sessions_sum_duration_seconds ?? 0),
            ]);

        return Inertia::render('History', [
            'rows' => $rows,
            'stats' => [
                'week_count' => $weekly['count'],
                'week_change_percent' => $weekly['change_percent'],
                'total_focus_seconds' => $stats->totalFocusSeconds($user),
                'longest_streak_days' => $stats->longestStreakDays($user),
            ],
            'filters' => $request->only(['q', 'category_id', 'days', 'priority']),
            'categories' => $user->categories()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
