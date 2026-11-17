<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'name'        => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status'      => fake()->randomElement(['pending', 'in_progress', 'done']),
            'start_date'  => fake()->dateTimeBetween('-1 month', 'now'),
            'due_date'    => fake()->dateTimeBetween('now', '+3 months'),
        ];
    }
}