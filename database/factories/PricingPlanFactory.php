<?php

namespace Database\Factories;

use App\Models\PricingPlan;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PricingPlan>
 */
class PricingPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'profile_id' => Profile::query()->value('id'),
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'title' => Str::title($title),
            'tagline' => fake()->sentence(5),
            'description' => fake()->sentence(12),
            'price' => fake()->randomElement([3_500_000, 7_500_000, 15_000_000]),
            'currency' => 'IDR',
            'billing_period' => 'project',
            'is_contact_for_price' => false,
            'call_to_action_label' => 'Konsultasikan proyek',
            'call_to_action_url' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'is_active' => true,
        ];
    }
}
