<?php

namespace Database\Factories;

use App\Models\ManagedProject;
use App\Models\ProjectTechnology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectTechnology>
 */
class ProjectTechnologyFactory extends Factory
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
            'name' => fake()->randomElement(['Laravel', 'PostgreSQL', 'Redis', 'Vue.js', 'Docker']),
            'category' => fake()->randomElement(['Backend', 'Database', 'Frontend', 'Infrastructure']),
            'version' => (string) fake()->numberBetween(1, 20),
            'notes' => fake()->sentence(),
        ];
    }
}
