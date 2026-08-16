<?php

use App\Models\Article;
use App\Models\ContentPage;
use App\Models\SeoMetadata;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('menampilkan url publik dan konten yang dapat diindeks', function (): void {
    $author = User::factory()->create();
    $publishedArticle = Article::query()->create([
        'author_id' => $author->id,
        'slug' => 'artikel-sudah-terbit',
        'title' => 'Artikel Sudah Terbit',
        'content' => 'Konten artikel publik.',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);
    $publishedPage = ContentPage::query()->create([
        'author_id' => $author->id,
        'slug' => 'syarat-layanan',
        'title' => 'Syarat Layanan',
        'content' => 'Syarat layanan publik.',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get(route('sitemap'));

    $response
        ->assertSuccessful()
        ->assertSee(route('home'), false)
        ->assertSee(route('about'), false)
        ->assertSee(route('projects.index'), false)
        ->assertSee(route('blog.index'), false)
        ->assertSee(route('pricing.index'), false)
        ->assertSee(route('blog.show', ['article' => $publishedArticle->slug]), false)
        ->assertSee(route('pages.show', ['contentPage' => $publishedPage->slug]), false);

    expect($response->headers->get('Content-Type'))->toContain('text/xml');
});

it('mengecualikan draft artikel terjadwal dan konten noindex', function (): void {
    $author = User::factory()->create();
    $draftArticle = Article::query()->create([
        'author_id' => $author->id,
        'slug' => 'artikel-draft',
        'title' => 'Artikel Draft',
        'content' => 'Belum diterbitkan.',
        'status' => 'draft',
    ]);
    $scheduledArticle = Article::query()->create([
        'author_id' => $author->id,
        'slug' => 'artikel-terjadwal',
        'title' => 'Artikel Terjadwal',
        'content' => 'Belum waktunya terbit.',
        'status' => 'published',
        'published_at' => now()->addDay(),
    ]);
    $undatedArticle = Article::query()->create([
        'author_id' => $author->id,
        'slug' => 'artikel-tanpa-tanggal',
        'title' => 'Artikel Tanpa Tanggal',
        'content' => 'Belum memiliki tanggal publikasi.',
        'status' => 'published',
        'published_at' => null,
    ]);
    $noindexArticle = Article::query()->create([
        'author_id' => $author->id,
        'slug' => 'artikel-noindex',
        'title' => 'Artikel Noindex',
        'content' => 'Tidak boleh diindeks.',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);
    SeoMetadata::query()->create([
        'seoable_type' => Article::class,
        'seoable_id' => $noindexArticle->id,
        'robots_index' => false,
        'robots_follow' => false,
    ]);

    $this->get(route('sitemap'))
        ->assertSuccessful()
        ->assertDontSee(route('blog.show', ['article' => $draftArticle->slug]), false)
        ->assertDontSee(route('blog.show', ['article' => $scheduledArticle->slug]), false)
        ->assertDontSee(route('blog.show', ['article' => $undatedArticle->slug]), false)
        ->assertDontSee(route('blog.show', ['article' => $noindexArticle->slug]), false);
});

it('mendaftarkan sitemap pada robots txt', function (): void {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)->toContain('Sitemap: https://naf-dreams.my.id/sitemap.xml');
});
