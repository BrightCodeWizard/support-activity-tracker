<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityUpdate>
 */
class ActivityUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'activity_id' => Activity::factory(),
            'user_id' => User::factory(),
            'update_date' => fake()->dateTimeBetween('-14 days', 'today')->format('Y-m-d'),
            'status' => fake()->randomElement(['done', 'pending']),
            'remark' => fake()->optional(0.8)->sentence(),
        ];
    }
}
