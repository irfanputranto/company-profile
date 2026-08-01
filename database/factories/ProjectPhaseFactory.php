<?php

namespace Database\Factories;

use App\Models\ManagedProject;
use App\Models\ProjectPhase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectPhase>
 */
class ProjectPhaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'managed_project_id' => ManagedProject::factory(),
            'name' => 'Phase '.fake()->numberBetween(1, 5),
            'description' => fake()->sentence(10),
            'deliverables' => fake()->sentence(8),
            'status' => 'in_progress',
            'progress' => fake()->numberBetween(10, 90),
            'started_at' => today()->subWeek(),
            'due_at' => today()->addMonth(),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
