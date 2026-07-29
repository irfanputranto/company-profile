<?php

namespace Database\Factories;

use App\Models\Feature;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Feature>
 */
class FeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'profile_id' => Profile::query()->value('id'),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'title' => $title,
            'description' => fake()->sentence(12),
            'icon' => 'sparkles',
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'is_active' => true,
        ];
    }
}
