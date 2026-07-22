<?php

use App\Models\Category;
use App\Models\FocusSession;
use App\Models\Task;
use App\Models\User;
use App\Models\UserPreference;

test('task belongs to user and category and has sessions', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create(['name' => 'Work']);
    $task = Task::factory()->for($user)->for($category)->create();
    $session = FocusSession::factory()->for($user)->for($task)->create();

    expect($task->user->is($user))->toBeTrue()
        ->and($task->category->is($category))->toBeTrue()
        ->and($task->sessions->first()->is($session))->toBeTrue()
        ->and($task->isCompleted())->toBeFalse();
});

test('user preferences are created on demand with defaults', function () {
    $user = User::factory()->create();
    $prefs = $user->getOrCreatePreferences();

    expect($prefs)->toBeInstanceOf(UserPreference::class)
        ->and($prefs->focus_minutes)->toBe(25)
        ->and($prefs->short_break_minutes)->toBe(5)
        ->and($prefs->long_break_minutes)->toBe(15)
        ->and($prefs->focus_tone)->toBe('soft-bell')
        ->and($prefs->break_tone)->toBe('upbeat-chime')
        ->and($prefs->browser_notifications)->toBeTrue()
        ->and($prefs->zen_mode)->toBeFalse();
});

test('deleting a user cascades to domain rows', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    FocusSession::factory()->for($user)->for($task)->create();
    $user->getOrCreatePreferences();

    $user->delete();

    expect(Task::count())->toBe(0)
        ->and(FocusSession::count())->toBe(0)
        ->and(UserPreference::count())->toBe(0);
});
