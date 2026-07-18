<?php

namespace Database\Factories;

use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    public function definition(): array
    {
        return [
            'focus_minutes' => 25,
            'short_break_minutes' => 5,
            'long_break_minutes' => 15,
            'focus_tone' => 'soft-bell',
            'break_tone' => 'upbeat-chime',
            'browser_notifications' => true,
            'zen_mode' => false,
        ];
    }
}
