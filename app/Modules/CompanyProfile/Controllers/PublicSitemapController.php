<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ContentPage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class PublicSitemapController extends Controller
{
    public function __invoke(): Sitemap
    {
        $sitemap = Sitemap::create();

        $this->addStaticUrls($sitemap);
        $this->addContentPages($sitemap);
        $this->addArticles($sitemap);

        return $sitemap;
    }

    private function addStaticUrls(Sitemap $sitemap): void
    {
        /** @var list<array{route: string, frequency: string, priority: float}> $urls */
        $urls = [
            ['route' => 'home', 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'priority' => 1.0],
            ['route' => 'blog.index', 'frequency' => Url::CHANGE_FREQUENCY_DAILY, 'priority' => 0.9],
            ['route' => 'about', 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.8],
            ['route' => 'projects.index', 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'priority' => 0.8],
            ['route' => 'pricing.index', 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY, 'priority' => 0.7],
        ];

        foreach ($urls as $url) {
            $sitemap->add(
                Url::create(route($url['route']))
                    ->setChangeFrequency($url['frequency'])
                    ->setPriority($url['priority']),
            );
        }
    }

    private function addContentPages(Sitemap $sitemap): void
    {
        ContentPage::query()
            ->select(['id', 'slug', 'updated_at'])
            ->publiclyVisible()
            ->whereDoesntHave(
                'seoMetadata',
                fn (Builder $query): Builder => $query->where('robots_index', false),
            )
            ->oldest('id')
            ->each(function (ContentPage $contentPage) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('pages.show', ['contentPage' => $contentPage->slug]))
                        ->setLastModificationDate($contentPage->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.6),
                );
            });
    }

    private function addArticles(Sitemap $sitemap): void
    {
        Article::query()
            ->select(['id', 'slug', 'published_at', 'updated_at'])
            ->publiclyVisible()
            ->whereDoesntHave(
                'seoMetadata',
                fn (Builder $query): Builder => $query->where('robots_index', false),
            )
            ->latest('published_at')
            ->each(function (Article $article) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('blog.show', ['article' => $article->slug]))
                        ->setLastModificationDate($article->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.8),
                );
            });
    }
}
