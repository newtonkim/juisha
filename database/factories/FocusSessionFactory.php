<?php

namespace Database\Factories;

use App\Models\FocusSession;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FocusSessionFactory extends Factory
{
    protected $model = FocusSession::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'started_at' => now()->subMinutes(25),
            'ended_at' => now(),
            'duration_seconds' => 1500,
            'completed' => true,
        ];
    }
}
