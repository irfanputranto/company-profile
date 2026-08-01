<?php

use App\Models\Article;
use App\Models\SeoMetadata;
use App\Models\User;
use Database\Seeders\PopularContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    User::factory()->create([
        'email' => 'admin@example.test',
        'name' => 'Administrator',
    ]);
});

it('menyediakan lima artikel populer 2026 beserta tag sumber dan metadata SEO', function (): void {
    $this->seed(PopularContentSeeder::class);
    $this->seed(PopularContentSeeder::class);

    $articles = Article::query()
        ->with(['tags', 'seoMetadata'])
        ->whereIn('slug', popularContentSlugs())
        ->get();

    expect($articles)->toHaveCount(5)
        ->and($articles->every(fn (Article $article): bool => $article->status === 'published'))
        ->toBeTrue()
        ->and($articles->every(fn (Article $article): bool => $article->is_featured))
        ->toBeTrue()
        ->and($articles->every(fn (Article $article): bool => $article->tags->isNotEmpty()))
        ->toBeTrue()
        ->and($articles->every(fn (Article $article): bool => Str::contains($article->content, 'Sumber tepercaya:')))
        ->toBeTrue()
        ->and($articles->every(fn (Article $article): bool => $article->seoMetadata instanceof SeoMetadata))
        ->toBeTrue()
        ->and($articles->every(fn (Article $article): bool => $article->seoMetadata->structured_data['@type'] === 'TechArticle'))
        ->toBeTrue();
});

it('menerapkan SEO artikel pada head halaman publik', function (): void {
    $this->seed(PopularContentSeeder::class);

    $article = Article::query()
        ->where('slug', popularContentSlugs()[0])
        ->firstOrFail();

    $this->get(route('blog.show', ['article' => $article->slug]))
        ->assertSuccessful()
        ->assertSee('<title>Upgrade Laravel 13 yang Aman di 2026</title>', false)
        ->assertSee('<link rel="canonical" href="'.route('blog.show', ['article' => $article->slug]).'">', false)
        ->assertSee('<meta property="og:type" content="article">', false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('https://laravel.com/docs/13.x/releases');
});

/** @return list<string> */
function popularContentSlugs(): array
{
    return [
        'laravel-13-panduan-upgrade-aman-2026',
        'php-85-fitur-backend-modern-2026',
        'postgresql-18-production-2026',
        'checklist-keamanan-web-owasp-2026',
        'passkeys-webauthn-level-3-2026',
    ];
}
