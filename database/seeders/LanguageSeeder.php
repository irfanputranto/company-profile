<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'id', 'name' => 'Indonesian', 'native_name' => 'Bahasa Indonesia', 'sort_order' => 1],
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'sort_order' => 2],
        ];

        foreach ($languages as $language) {
            Language::query()->updateOrCreate(
                ['code' => $language['code']],
                [...$language, 'direction' => 'ltr', 'is_active' => true],
            );
        }

        if (! Language::query()->where('is_default', true)->exists()) {
            $defaultCode = Language::query()->where('code', config('app.locale'))->exists()
                ? (string) config('app.locale')
                : 'id';

            Language::query()->where('code', $defaultCode)->update(['is_default' => true]);
        }

        $defaultCode = Language::query()->where('is_default', true)->value('code') ?? config('app.locale');
        SiteSetting::query()->firstOrCreate(
            ['key' => 'site.default_language'],
            ['group' => 'localization', 'type' => 'string', 'value' => $defaultCode, 'is_public' => true],
        );
    }
}
