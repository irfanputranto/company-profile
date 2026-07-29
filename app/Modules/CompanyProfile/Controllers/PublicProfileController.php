<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Profile;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function __invoke(): View
    {
        $profile = Profile::query()
            ->with([
                'contentTranslations.language',
                'services' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
                'skills' => fn ($query) => $query
                    ->with(['contentTranslations.language', 'category.contentTranslations.language'])
                    ->where('is_active', true)
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->limit(6),
                'features' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->limit(6),
                'projects' => fn ($query) => $query
                    ->with(['contentTranslations.language', 'skills.contentTranslations.language'])
                    ->where('is_active', true)
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->limit(6),
                'experiences' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->latest('started_at')
                    ->limit(5),
                'socialLinks' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
                'testimonials' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->limit(3),
                'faqs' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit(6),
            ])
            ->where('is_active', true)
            ->first();

        $articles = Article::query()
            ->with('contentTranslations.language')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('welcome', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'skills' => $profile?->skills ?? collect(),
            'features' => $profile?->features ?? collect(),
            'projects' => $profile?->projects ?? collect(),
            'experiences' => $profile?->experiences ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
            'testimonials' => $profile?->testimonials ?? collect(),
            'faqs' => $profile?->faqs ?? collect(),
            'articles' => $articles,
        ]);
    }
}
