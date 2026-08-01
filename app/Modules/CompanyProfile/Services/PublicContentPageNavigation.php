<?php

namespace App\Modules\CompanyProfile\Services;

use App\Models\ContentPage;
use Illuminate\Database\Eloquent\Collection;

class PublicContentPageNavigation
{
    /** @var Collection<int, ContentPage>|null */
    private ?Collection $pages = null;

    /** @return Collection<int, ContentPage> */
    public function pages(): Collection
    {
        return $this->pages ??= ContentPage::query()
            ->with(['contentTranslations' => fn ($query) => $query
                ->where('field', 'title')
                ->with('language')])
            ->publiclyVisible()
            ->where('show_in_navigation', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'slug', 'title', 'sort_order', 'published_at']);
    }
}
