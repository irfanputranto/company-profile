<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PermissionSeeder::class,
            LanguageSeeder::class,
            PersonalProfileSeeder::class,
            FeatureSeeder::class,
            PricingPlanSeeder::class,
            PopularContentSeeder::class,
            PersonalProfileTranslationSeeder::class,
        ]);
    }
}
