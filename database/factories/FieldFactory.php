<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'name' => fake()->word(),
        'crop_type' => fake()->word(),
        'planting_date' => fake()->date(),
        'current_stage' => 'planted',
        'agent_id' => \App\Models\User::factory(), // Automatically create an agent if none provided
    ];
}

}