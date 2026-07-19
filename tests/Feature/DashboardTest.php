<?php

use App\Models\{Task, User};
use Inertia\Testing\AssertableInertia as Assert;

test('dashboard shows today tasks ordered pending-first with stats', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->completed()->create(['title' => 'Done one']);
    Task::factory()->for($user)->create(['title' => 'High one', 'priority' => 'high']);
    Task::factory()->for($user)->create(['title' => 'Low one', 'priority' => 'low']);
    Task::factory()->for($user)->create(['created_at' => now()->subDays(2)]); // excluded

    $this->actingAs($user)->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks', 3)
            ->where('tasks.0.title', 'High one')
            ->where('tasks.2.title', 'Done one')
            ->where('stats.total', 3)
            ->where('stats.completed', 1)
            ->has('categories')
            ->where('focus_minutes', 25));
});

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
