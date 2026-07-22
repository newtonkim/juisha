<?php

use App\Models\FocusSession;
use App\Models\Task;
use App\Models\User;
use App\Services\FocusStats;

beforeEach(fn () => $this->travelTo(now()->parse('2026-07-15 09:00'))); // Wednesday

test('daily progress counts today tasks, deep work seconds, and focus score', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->count(2)->create();                       // created today
    Task::factory()->for($user)->completed()->create();                    // created+completed today
    Task::factory()->for($user)->create(['created_at' => now()->subDays(3), 'due_at' => now()]); // due today
    Task::factory()->for($user)->create(['created_at' => now()->subDays(3)]); // not today

    $task = $user->tasks()->first();
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subHour(), 'duration_seconds' => 1500, 'completed' => true]);
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subHours(2), 'duration_seconds' => 900, 'completed' => false]);
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subDays(2)]); // not today

    $stats = (new FocusStats)->dailyProgress($user);

    expect($stats)->toBe(['completed' => 1, 'total' => 4, 'deep_work_seconds' => 2400, 'focus_score' => 50]);
});

test('weekly completed compares against previous week', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->count(3)->completed()->create();
    Task::factory()->for($user)->count(2)->create([
        'status' => 'completed', 'completed_at' => now()->subWeek(),
    ]);

    expect((new FocusStats)->weeklyCompleted($user))->toBe(['count' => 3, 'change_percent' => 50]);
});

test('longest streak counts consecutive days with completed sessions', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    foreach ([1, 2, 3, 5, 6, 9] as $daysAgo) {
        FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subDays($daysAgo)]);
    }
    FocusSession::factory()->for($user)->for($task)->create(['started_at' => now()->subDays(4), 'completed' => false]); // abandoned, breaks nothing

    expect((new FocusStats)->longestStreakDays($user))->toBe(3);
    expect((new FocusStats)->totalFocusSeconds($user))->toBe(7 * 1500);
});
