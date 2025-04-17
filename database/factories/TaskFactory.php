<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'assigned_user_id' => User::inRandomOrder()->first()->id,
            'due_date'    => Carbon::instance($this->faker->dateTimeBetween('now', '+1 month'))->format('Y-m-d'),
            'status'      => $this->faker->randomElement(['pending', 'completed', 'canceled']),
            'created_by'  => User::inRandomOrder()->first()->id,
        ];
    }
}
