<?php

namespace Database\Factories;

use App\Models\ManagedProject;
use App\Models\ProjectDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectDocument>
 */
class ProjectDocumentFactory extends Factory
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
            'uuid' => fake()->uuid(),
            'category' => 'requirement',
            'title' => fake()->sentence(3),
            'disk' => 'local',
            'path' => 'project-documents/'.fake()->uuid().'.pdf',
            'original_name' => 'requirement.pdf',
            'mime_type' => 'application/pdf',
            'byte_size' => 1024,
        ];
    }
}
