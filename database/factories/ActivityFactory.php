<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['SMS', 'Batch Processing', 'Interface Monitoring', 'Core Banking', 'Reports']),
            'frequency' => fake()->randomElement(['daily', 'weekly']),
            'is_active' => true,
        ];
    }
}
