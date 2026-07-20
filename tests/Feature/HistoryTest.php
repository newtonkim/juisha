<?php

use App\Models\{Category, FocusSession, Task, User};
use Inertia\Testing\AssertableInertia as Assert;

test('history lists completed tasks with focused seconds and stats', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->completed()->create(['title' => 'Ship report']);
    FocusSession::factory()->for($user)->for($task)->count(2)->create(['duration_seconds' => 900]);
    Task::factory()->for($user)->create(); // pending — excluded

    $this->actingAs($user)->get('/history')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('History')
            ->has('rows', 1)
            ->where('rows.0.title', 'Ship report')
            ->where('rows.0.focused_seconds', 1800)
            ->where('stats.week_count', 1)
            ->where('stats.total_focus_seconds', 1800));
});

test('history filters by search, priority, and category', function () {
    $user = User::factory()->create();
    $cat = Category::factory()->for($user)->create();
    Task::factory()->for($user)->completed()->create(['title' => 'Alpha work', 'priority' => 'high', 'category_id' => $cat->id]);
    Task::factory()->for($user)->completed()->create(['title' => 'Beta work', 'priority' => 'low']);

    $this->actingAs($user)->get('/history?q=Alpha')
        ->assertInertia(fn (Assert $p) => $p->component('History')->has('rows', 1));
    $this->actingAs($user)->get('/history?priority=low')
        ->assertInertia(fn (Assert $p) => $p->component('History')->has('rows', 1)->where('rows.0.title', 'Beta work'));
    $this->actingAs($user)->get("/history?category_id={$cat->id}")
        ->assertInertia(fn (Assert $p) => $p->component('History')->has('rows', 1)->where('rows.0.title', 'Alpha work'));
});
