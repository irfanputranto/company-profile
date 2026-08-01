<?php

namespace Database\Factories;

use App\Models\ProjectFeature;
use App\Models\ProjectPhase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectFeature>
 */
class ProjectFeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_phase_id' => ProjectPhase::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(8),
            'acceptance_criteria' => fake()->sentence(8),
            'status' => 'backlog',
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
