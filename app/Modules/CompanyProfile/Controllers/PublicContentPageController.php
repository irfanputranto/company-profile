<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use App\Models\Profile;
use App\Modules\CompanyProfile\Services\ArticleContentFormatter;
use Illuminate\View\View;

class PublicContentPageController extends Controller
{
    public function __invoke(ContentPage $contentPage, ArticleContentFormatter $contentFormatter): View
    {
        abort_unless($contentPage->isPubliclyVisible(), 404);

        $contentPage->load([
            'contentTranslations.language',
            'seoMetadata.contentTranslations.language',
        ]);
        $profile = $this->profile();

        return view('public.pages.show', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
            'page' => $contentPage,
            'template' => in_array($contentPage->template, ['default', 'legal', 'landing'], true)
                ? $contentPage->template
                : 'default',
            'formattedContent' => $contentFormatter->format((string) $contentPage->translated('content')),
        ]);
    }

    private function profile(): ?Profile
    {
        return Profile::query()
            ->with([
                'contentTranslations.language',
                'logoMedia',
                'faviconMedia',
                'services' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
                'socialLinks' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->where('is_active', true)
            ->first();
    }
}
