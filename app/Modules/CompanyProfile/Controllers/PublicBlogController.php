<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Profile;
use Illuminate\View\View;

class PublicBlogController extends Controller
{
    public function index(): View
    {
        $profile = $this->profile();
        $articles = Article::query()
            ->with('contentTranslations.language')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(9);

        return view('public.blog.index', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
            'articles' => $articles,
        ]);
    }

    public function show(Article $article): View
    {
        abort_unless(
            $article->status === 'published' && $article->published_at?->isPast(),
            404,
        );

        $article->load('contentTranslations.language');
        $profile = $this->profile();

        return view('public.blog.show', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
            'article' => $article,
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
