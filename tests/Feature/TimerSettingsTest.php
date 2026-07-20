<?php

use App\Models\User;

test('user can view and update timer preferences', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/settings/timer')->assertOk();

    $this->actingAs($user)->patch('/settings/timer', [
        'focus_minutes' => 50, 'short_break_minutes' => 10, 'long_break_minutes' => 20,
        'focus_tone' => 'deep-gong', 'break_tone' => 'gentle-marimba',
        'browser_notifications' => false, 'zen_mode' => true,
    ])->assertRedirect();

    expect($user->preferences->refresh())
        ->focus_minutes->toBe(50)->zen_mode->toBeTrue()->focus_tone->toBe('deep-gong');
});

test('out of range durations are rejected', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->patch('/settings/timer', [
        'focus_minutes' => 5, 'short_break_minutes' => 30, 'long_break_minutes' => 20,
        'focus_tone' => 'soft-bell', 'break_tone' => 'upbeat-chime',
        'browser_notifications' => true, 'zen_mode' => false,
    ])->assertSessionHasErrors(['focus_minutes', 'short_break_minutes']);
});
