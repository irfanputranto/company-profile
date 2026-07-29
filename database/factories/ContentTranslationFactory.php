<?php

namespace Database\Factories;

use App\Models\ContentTranslation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentTranslation>
 */
class ContentTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'language_id' => Language::factory(),
            'translatable_type' => 'App\\Models\\Service',
            'translatable_id' => fake()->numberBetween(1, 1000),
            'field' => 'title',
            'value' => fake()->sentence(),
        ];
    }
}
