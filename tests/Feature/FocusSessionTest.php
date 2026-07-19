<?php

use App\Models\{FocusSession, Task, User};

test('starting a session closes any open one and creates a new row', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    $stale = FocusSession::factory()->for($user)->for($task)->create([
        'started_at' => now()->subMinutes(10), 'ended_at' => null, 'duration_seconds' => 0, 'completed' => false,
    ]);

    $this->actingAs($user)->post("/tasks/{$task->id}/sessions")
        ->assertRedirect("/focus/{$task->id}");

    expect($stale->refresh()->ended_at)->not->toBeNull()
        ->and($stale->completed)->toBeFalse()
        ->and($task->sessions()->whereNull('ended_at')->count())->toBe(1);
});

test('finishing a session records duration capped at wall time and can complete the task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['session_minutes' => 25]);
    $session = FocusSession::factory()->for($user)->for($task)->create([
        'started_at' => now()->subMinutes(25), 'ended_at' => null, 'duration_seconds' => 0, 'completed' => false,
    ]);

    $this->actingAs($user)->patch("/sessions/{$session->id}/finish", [
        'elapsed_seconds' => 99999, 'completed' => true, 'complete_task' => true,
    ])->assertRedirect('/dashboard');

    $session->refresh();
    expect($session->completed)->toBeTrue()
        ->and($session->duration_seconds)->toBeLessThanOrEqual(25 * 60 + 5)
        ->and($task->refresh()->status)->toBe('completed');
});

test('focus page shows task and active session', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $this->actingAs($user)->get("/focus/{$task->id}")->assertOk();
    $this->actingAs(User::factory()->create())->get("/focus/{$task->id}")->assertForbidden();
});

test('database rejects a second open focus session for the same user', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $task->sessions()->create([
        'user_id' => $user->id,
        'started_at' => now(),
        'ended_at' => null,
    ]);

    expect(fn () => FocusSession::create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'started_at' => now(),
        'ended_at' => null,
    ]))->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});
