<?php

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->administrator = User::factory()->create([
        'uuid' => (string) \Illuminate\Support\Str::uuid(),
        'username' => 'tinymce-admin',
        'is_active' => true,
    ]);
    
    grantMasterPermissions($this->administrator, 'articles');
    $this->administrator->givePermissionTo(Permission::findOrCreate('view_analytics', 'web'));
    
    $this->profile = \App\Models\Profile::query()->create([
        'slug' => 'test-profile',
        'public_name' => 'Test Profile',
        'headline' => 'Software Engineer',
        'timezone' => 'Asia/Jakarta',
        'availability_status' => 'available',
        'years_experience' => 7,
        'is_active' => true,
    ]);
    
    $this->actingAs($this->administrator);
});

it('merender TinyMCE editor di form create artikel', function () {
    $response = $this->get(route('company-profile.content.create', ['resource' => 'articles']))
        ->assertSuccessful()
        ->assertSee('Tambah Artikel');
    
    // Verifikasi TinyMCE CDN dimuat
    $response->assertSee('tinymce', false);
    
    // Verifikasi textarea untuk content memiliki class/attribut TinyMCE
    $response->assertSee('id="content"', false);
});

it('merender TinyMCE editor di form edit artikel', function () {
    $article = Article::query()->create([
        'author_id' => $this->administrator->id,
        'article_category_id' => null,
        'title' => 'Test Article',
        'slug' => 'test-article',
        'excerpt' => 'Test excerpt',
        'content' => '<p>Isi artikel dengan <strong>HTML</strong>.</p>',
        'status' => 'draft',
        'is_featured' => false,
        'reading_time_minutes' => 5,
        'published_at' => null,
    ]);
    
    $response = $this->get(route('company-profile.content.edit', [
        'resource' => 'articles',
        'record' => $article->id,
    ]))
        ->assertSuccessful()
        ->assertSee('Edit Artikel');
    
    // Verifikasi TinyMCE CDN dimuat
    $response->assertSee('tinymce', false);
    
    // Verifikasi konten HTML sudah ada di textarea
    $response->assertSee('Isi artikel dengan <strong>HTML</strong>.', false);
});

it('menyimpan artikel dengan konten HTML dari TinyMCE', function () {
    $htmlContent = '<h2>Judul</h2><p>Paragraf dengan <em>italic</em> dan <strong>bold</strong>.</p><ul><li>Item 1</li><li>Item 2</li></ul>';
    
    $this->post(route('company-profile.content.store', ['resource' => 'articles']), [
        'author_id' => null,
        'article_category_id' => null,
        'title' => 'Artikel dengan TinyMCE',
        'slug' => '',
        'excerpt' => 'Excerpt artikel',
        'content' => $htmlContent,
        'tag_ids' => [],
        'status' => 'draft',
        'is_featured' => 1,
        'reading_time_minutes' => 3,
        'published_at' => null,
    ])->assertRedirect(route('company-profile.content.index', ['resource' => 'articles']));
    
    $article = Article::query()->where('slug', 'artikel-dengan-tinymce')->firstOrFail();
    expect($article->content)->toBe($htmlContent);
});

it('memperbarui artikel dengan konten HTML baru dari TinyMCE', function () {
    $article = Article::query()->create([
        'author_id' => $this->administrator->id,
        'article_category_id' => null,
        'title' => 'Artikel Lama',
        'slug' => 'artikel-lama',
        'excerpt' => 'Excerpt lama',
        'content' => '<p>Konten lama.</p>',
        'status' => 'draft',
        'is_featured' => false,
        'reading_time_minutes' => 2,
        'published_at' => null,
    ]);
    
    $newHtmlContent = '<h1>Judul Baru</h1><p>Konten <code>baru</code> dengan format.</p>';
    
    $this->put(route('company-profile.content.update', [
        'resource' => 'articles',
        'record' => $article->id,
    ]), [
        'author_id' => $article->author_id,
        'article_category_id' => null,
        'title' => 'Artikel Diperbarui',
        'slug' => $article->slug,
        'excerpt' => 'Excerpt baru',
        'content' => $newHtmlContent,
        'tag_ids' => [],
        'status' => 'published',
        'is_featured' => 1,
        'reading_time_minutes' => 4,
        'published_at' => now()->toDateTimeString(),
    ])->assertRedirect(route('company-profile.content.index', ['resource' => 'articles']));
    
    $article->refresh();
    expect($article->content)->toBe($newHtmlContent)
        ->and($article->title)->toBe('Artikel Diperbarui')
        ->and($article->status)->toBe('published');
});