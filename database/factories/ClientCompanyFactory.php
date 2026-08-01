<?php

namespace Database\Factories;

use App\Models\ClientCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientCompany>
 */
class ClientCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'notes' => fake()->sentence(),
        ];
    }
}
