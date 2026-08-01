<?php

namespace Database\Factories;

use App\Models\ClientCompany;
use App\Models\ManagedProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ManagedProject>
 */
class ManagedProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_company_id' => ClientCompany::factory(),
            'code' => 'PRJ-'.fake()->unique()->numerify('######'),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraphs(2, true),
            'status' => 'in_progress',
            'started_at' => today()->subMonth(),
            'due_at' => today()->addMonths(2),
            'contract_value' => 25_000_000,
            'estimated_cost' => 18_000_000,
            'currency' => 'IDR',
        ];
    }
}
