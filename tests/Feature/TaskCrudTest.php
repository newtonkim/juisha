<?php

use App\Models\Category;
use App\Models\Task;
use App\Models\User;

test('a user can create a task with all fields', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user)->post('/tasks', [
        'title' => 'Draft Q3 strategy',
        'description' => 'Core OKRs',
        'due_at' => now()->addHours(3)->toDateTimeString(),
        'priority' => 'high',
        'session_minutes' => 45,
        'notify_on_end' => true,
        'five_minute_warning' => true,
        'category_id' => $category->id,
    ])->assertRedirect();

    expect($user->tasks()->first())
        ->title->toBe('Draft Q3 strategy')
        ->priority->toBe('high')
        ->session_minutes->toBe(45)
        ->category_id->toBe($category->id);
});

test('validation rejects bad priority and foreign category', function () {
    $user = User::factory()->create();
    $foreign = Category::factory()->create(); // other user's

    $this->actingAs($user)->post('/tasks', [
        'title' => 'x', 'priority' => 'urgent', 'session_minutes' => 25, 'category_id' => $foreign->id,
    ])->assertSessionHasErrors(['priority', 'category_id']);
});

test('toggle completes and reopens a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $this->actingAs($user)->patch("/tasks/{$task->id}/toggle")->assertRedirect();
    expect($task->refresh())->status->toBe('completed')->completed_at->not->toBeNull();

    $this->actingAs($user)->patch("/tasks/{$task->id}/toggle")->assertRedirect();
    expect($task->refresh())->status->toBe('pending')->completed_at->toBeNull();
});

test('users cannot touch other users tasks', function () {
    $task = Task::factory()->create();
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->patch("/tasks/{$task->id}/toggle")->assertForbidden();
    $this->actingAs($intruder)->delete("/tasks/{$task->id}")->assertForbidden();
});

test('a user can create a category inline and duplicates are rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/categories', ['name' => 'Deep Work'])->assertRedirect();
    expect($user->categories()->pluck('name'))->toContain('Deep Work');

    $this->actingAs($user)->post('/categories', ['name' => 'Deep Work'])
        ->assertSessionHasErrors('name');
});
