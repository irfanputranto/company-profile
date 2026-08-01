<?php

namespace Database\Factories;

use App\Models\ManagedProject;
use App\Models\ProjectServer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectServer>
 */
class ProjectServerFactory extends Factory
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
            'name' => 'Production Server',
            'provider' => fake()->company(),
            'environment' => 'production',
            'host' => fake()->domainName(),
            'port' => 22,
            'username' => 'deploy',
            'password' => fake()->password(20),
            'billing_cycle' => 'yearly',
            'base_price' => 2_000_000,
            'selling_price' => 2_500_000,
            'currency' => 'IDR',
            'purchased_at' => today(),
            'expires_at' => today()->addYear(),
            'reminder_days' => 30,
            'status' => 'active',
        ];
    }
}
