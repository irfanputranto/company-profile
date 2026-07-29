<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = fake()->unique()->languageCode();

        return [
            'code' => $code,
            'name' => fake()->languageCode(),
            'native_name' => strtoupper($code),
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
