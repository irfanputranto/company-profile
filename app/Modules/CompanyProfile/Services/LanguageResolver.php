<?php

namespace App\Modules\CompanyProfile\Services;

use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class LanguageResolver
{
    public const CACHE_KEY = 'company-profile.languages';

    /** @return Collection<int, Language> */
    public function activeLanguages(): Collection
    {
        if (! Schema::hasTable('languages')) {
            return collect();
        }

        return Cache::remember(
            self::CACHE_KEY,
            now()->addHour(),
            fn (): Collection => Language::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        );
    }

    public function defaultCode(): string
    {
        return $this->activeLanguages()->firstWhere('is_default', true)?->code
            ?? (string) config('app.locale');
    }

    public function resolve(?string $requestedLocale): string
    {
        if ($requestedLocale && $this->activeLanguages()->contains('code', $requestedLocale)) {
            return $requestedLocale;
        }

        return $this->defaultCode();
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
