<?php

namespace App\Services;

use App\Models\User;

class FocusStats
{
    public function dailyProgress(User $user): array
    {
        $today = now()->toDateString();
        $tasks = $user->tasks()
            ->where(fn ($q) => $q->whereDate('due_at', $today)->orWhereDate('created_at', $today))
            ->get();
        $sessions = $user->focusSessions()->whereDate('started_at', $today)->get();
        $started = $sessions->count();

        return [
            'completed' => $tasks->where('status', 'completed')->count(),
            'total' => $tasks->count(),
            'deep_work_seconds' => (int) $sessions->sum('duration_seconds'),
            'focus_score' => $started > 0 ? (int) round($sessions->where('completed', true)->count() / $started * 100) : 0,
        ];
    }

    public function weeklyCompleted(User $user): array
    {
        $count = $this->completedBetween($user, now()->startOfWeek(), now()->endOfWeek());
        $previous = $this->completedBetween($user, now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());

        return [
            'count' => $count,
            'change_percent' => $previous > 0
                ? (int) round(($count - $previous) / $previous * 100)
                : ($count > 0 ? 100 : 0),
        ];
    }

    public function totalFocusSeconds(User $user): int
    {
        return (int) $user->focusSessions()->sum('duration_seconds');
    }

    public function longestStreakDays(User $user): int
    {
        $days = $user->focusSessions()->where('completed', true)
            ->pluck('started_at')->map(fn ($d) => $d->toDateString())->unique()->sort()->values();

        $best = $run = $days->isEmpty() ? 0 : 1;
        for ($i = 1; $i < $days->count(); $i++) {
            $run = now()->parse($days[$i - 1])->addDay()->toDateString() === $days[$i] ? $run + 1 : 1;
            $best = max($best, $run);
        }

        return $best;
    }

    private function completedBetween(User $user, $from, $to): int
    {
        return $user->tasks()->where('status', 'completed')->whereBetween('completed_at', [$from, $to])->count();
    }
}
