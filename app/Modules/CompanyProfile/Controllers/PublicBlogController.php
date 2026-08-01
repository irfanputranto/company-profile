<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Profile;
use App\Modules\CompanyProfile\Services\ArticleContentFormatter;
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

    public function show(Article $article, ArticleContentFormatter $contentFormatter): View
    {
        abort_unless(
            $article->status === 'published' && $article->published_at?->isPast(),
            404,
        );

        $article->load([
            'author:id,uuid,name,username,updated_at',
            'category.contentTranslations.language',
            'contentTranslations.language',
            'seoMetadata.contentTranslations.language',
            'tags.contentTranslations.language',
        ]);
        $profile = $this->profile();
        $relatedArticles = Article::query()
            ->with(['category.contentTranslations.language', 'contentTranslations.language'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->whereKeyNot($article->getKey())
            ->when(
                $article->article_category_id,
                fn ($query, int $categoryId) => $query->where('article_category_id', $categoryId),
            )
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.blog.show', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
            'article' => $article,
            'formattedContent' => $contentFormatter->format((string) $article->translated('content')),
            'relatedArticles' => $relatedArticles,
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
