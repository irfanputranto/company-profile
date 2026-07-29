<?php

namespace Database\Factories;

use App\Models\PageVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageVisit>
 */
class PageVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scope_type' => 'site',
            'scope_id' => 0,
            'route_name' => 'home',
            'path' => '/',
            'visitor_hash' => hash('sha256', fake()->uuid()),
            'session_hash' => hash('sha256', fake()->uuid()),
            'referrer_host' => fake()->optional()->domainName(),
            'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
            'country_code' => 'ID',
            'is_bot' => false,
            'occurred_at' => now(),
        ];
    }
}
