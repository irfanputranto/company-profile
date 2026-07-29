<?php

namespace App\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Str;

class PageGuideResolver
{
    public function __construct(
        private readonly Repository $config,
        private readonly Translator $translator,
    ) {}

    /** @return array<string, mixed> */
    public function resolve(?string $routeName): array
    {
        if (Str::is($this->config->get('page_guides.local_routes', []), $routeName ?? '')) {
            return [];
        }

        foreach ($this->config->get('page_guides.pages', []) as $guide) {
            if (Str::is($guide['routes'] ?? [], $routeName ?? '')) {
                return $this->translate($guide);
            }
        }

        return $this->translate($this->config->get('page_guides.fallback', []));
    }

    /** @param array<string, mixed> $guide
     * @return array<string, mixed>
     */
    private function translate(array $guide): array
    {
        $translation = $this->translator->get("admin.page_guides.{$guide['key']}");

        if (! is_array($translation)) {
            return [];
        }

        unset($guide['key'], $guide['routes']);

        return [...$translation, ...$guide];
    }
}
