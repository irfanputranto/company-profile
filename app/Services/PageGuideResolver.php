<?php

namespace App\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;

class PageGuideResolver
{
    public function __construct(private readonly Repository $config) {}

    /** @return array<string, mixed> */
    public function resolve(?string $routeName): array
    {
        if (Str::is($this->config->get('page_guides.local_routes', []), $routeName ?? '')) {
            return [];
        }

        foreach ($this->config->get('page_guides.pages', []) as $guide) {
            if (Str::is($guide['routes'] ?? [], $routeName ?? '')) {
                return $guide;
            }
        }

        return $this->config->get('page_guides.fallback', []);
    }
}
