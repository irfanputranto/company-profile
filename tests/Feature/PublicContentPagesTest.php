<?php

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ContentPage;
use App\Models\Feature;
use App\Models\Language;
use App\Models\PricingPlan;
use App\Models\Profile;
use App\Models\SeoMetadata;
use App\Models\Tag;
use App\Models\User;
use App\Modules\CompanyProfile\Services\LanguageResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Language::query()->create([
        'code' => 'id',
        'name' => 'Indonesian',
        'native_name' => 'Bahasa Indonesia',
        'direction' => 'ltr',
        'is_default' => true,
        'is_active' => true,
        'sort_order' => 1,
    ]);
    app(LanguageResolver::class)->forget();

    $this->user = User::factory()->create();
    $this->profile = Profile::query()->create([
        'user_id' => $this->user->id,
        'slug' => 'irfan',
        'public_name' => 'Irfan Putranto',
        'headline' => 'Software Engineer',
        'email' => 'irfan@example.test',
        'timezone' => 'Asia/Jakarta',
        'availability_status' => 'available',
        'years_experience' => 7,
        'is_active' => true,
    ]);
});

it('menampilkan menu utama dengan status aktif sesuai halaman', function (): void {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('x-data="publicNavigation(\'home\')"', false)
        ->assertSee(route('projects.index'), false)
        ->assertSee(route('about'), false)
        ->assertSee(route('blog.index'), false)
        ->assertSee(route('pricing.index'), false)
        ->assertSee('Beranda')
        ->assertSee('Tentang')
        ->assertSee('Blog')
        ->assertSee('Harga')
        ->assertSee(route('pricing.index'), false)
        ->assertSee('data-analytics-event="pricing"', false)
        ->assertDontSee('public-more-navigation', false)
        ->assertDontSee('Lainnya');

    $this->get(route('about'))
        ->assertSuccessful()
        ->assertSee('x-data="publicNavigation(\'about\')"', false);

    $this->get(route('pricing.index'))
        ->assertSuccessful()
        ->assertSee('x-data="publicNavigation(\'pricing\')"', false);
});

it('menampilkan paket harga beserta fitur yang dapat dikustomisasi', function (): void {
    $feature = Feature::query()->create([
        'profile_id' => $this->profile->id,
        'slug' => 'solusi-custom',
        'title' => 'Solusi custom',
        'description' => 'Aplikasi mengikuti proses bisnis.',
        'sort_order' => 1,
        'is_featured' => true,
        'is_active' => true,
    ]);
    $pricingPlan = PricingPlan::query()->create([
        'profile_id' => $this->profile->id,
        'slug' => 'professional',
        'title' => 'Professional',
        'tagline' => 'Untuk bisnis yang siap bertumbuh',
        'description' => 'Paket aplikasi custom dengan dukungan delivery.',
        'price' => 7500000,
        'currency' => 'IDR',
        'billing_period' => 'project',
        'call_to_action_label' => 'Konsultasi sekarang',
        'sort_order' => 2,
        'is_featured' => true,
        'is_active' => true,
    ]);
    $pricingPlan->features()->attach($feature);

    $this->get(route('pricing.index'))
        ->assertSuccessful()
        ->assertSee('Professional')
        ->assertSee('Solusi custom')
        ->assertSee('Konsultasi sekarang')
        ->assertSee('Paling populer');
});

it('menampilkan daftar artikel dan hanya membuka artikel yang sudah terbit', function (): void {
    $category = ArticleCategory::query()->create([
        'name' => 'Backend Engineering',
        'slug' => 'backend-engineering',
        'description' => 'Catatan backend dan arsitektur.',
    ]);
    $tag = Tag::query()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $publishedArticle = Article::query()->create([
        'author_id' => $this->user->id,
        'article_category_id' => $category->id,
        'slug' => 'membangun-aplikasi-bisnis',
        'title' => 'Membangun Aplikasi Bisnis',
        'excerpt' => 'Panduan memulai aplikasi custom.',
        'content' => "Mulai dari masalah bisnis dan hasil yang ingin dicapai.\n\nLangkah implementasi\n\n1. Petakan kebutuhan bisnis.\n2. Tentukan hasil yang terukur.\n\nSumber tepercaya:\n- Dokumentasi Laravel: https://laravel.com/docs/12.x",
        'status' => 'published',
        'reading_time_minutes' => 5,
        'published_at' => now()->subDay(),
    ]);
    $publishedArticle->tags()->attach($tag);
    Article::query()->create([
        'author_id' => $this->user->id,
        'article_category_id' => $category->id,
        'slug' => 'optimasi-backend-production',
        'title' => 'Optimasi Backend Production',
        'excerpt' => 'Langkah menjaga backend tetap cepat.',
        'content' => 'Ukur sebelum melakukan optimasi.',
        'status' => 'published',
        'reading_time_minutes' => 4,
        'published_at' => now()->subDays(2),
    ]);
    $draftArticle = Article::query()->create([
        'author_id' => $this->user->id,
        'article_category_id' => $category->id,
        'slug' => 'artikel-draft',
        'title' => 'Artikel Draft',
        'excerpt' => 'Belum boleh tampil.',
        'content' => 'Draft.',
        'status' => 'draft',
    ]);

    $this->get(route('blog.index'))
        ->assertSuccessful()
        ->assertSee('Membangun Aplikasi Bisnis')
        ->assertDontSee('Artikel Draft')
        ->assertSee('x-data="publicNavigation(\'blog\')"', false);

    $this->get(route('blog.show', $publishedArticle))
        ->assertSuccessful()
        ->assertSee('<meta name="description" content="Panduan memulai aplikasi custom.">', false)
        ->assertSee('Mulai dari masalah bisnis')
        ->assertSee('Panduan memulai aplikasi custom.')
        ->assertSee('Backend Engineering')
        ->assertSee('Laravel')
        ->assertSee('Irfan Putranto')
        ->assertSee('Dalam artikel ini')
        ->assertSee('id="langkah-implementasi"', false)
        ->assertSee('href="https://laravel.com/docs/12.x"', false)
        ->assertSee('Optimasi Backend Production')
        ->assertDontSee('Artikel Draft');

    $this->get(route('blog.show', $draftArticle))->assertNotFound();
});

it('menerbitkan halaman CMS dinamis dan menampilkannya pada dropdown navigasi', function (): void {
    $publishedPage = ContentPage::query()->create([
        'author_id' => $this->user->id,
        'slug' => 'kebijakan-privasi',
        'title' => 'Kebijakan Privasi',
        'template' => 'legal',
        'content' => "Informasi mengenai pengelolaan data pribadi.\n\nData yang dikumpulkan\n\nKami hanya mengumpulkan data yang diperlukan.",
        'status' => 'published',
        'show_in_navigation' => true,
        'sort_order' => 1,
        'published_at' => now()->subDay(),
    ]);
    SeoMetadata::query()->create([
        'seoable_type' => ContentPage::class,
        'seoable_id' => $publishedPage->id,
        'meta_title' => 'Kebijakan Privasi — Naf Dreams',
        'meta_description' => 'Penjelasan pengelolaan data pribadi.',
        'robots_index' => true,
        'robots_follow' => true,
        'twitter_card' => 'summary',
    ]);
    $hiddenPage = ContentPage::query()->create([
        'author_id' => $this->user->id,
        'slug' => 'informasi-internal',
        'title' => 'Informasi Internal',
        'template' => 'default',
        'content' => 'Halaman dapat dibuka tetapi tidak ditampilkan di menu.',
        'status' => 'published',
        'show_in_navigation' => false,
        'sort_order' => 2,
        'published_at' => now()->subDay(),
    ]);
    $draftPage = ContentPage::query()->create([
        'slug' => 'halaman-draft',
        'title' => 'Halaman Draft',
        'template' => 'default',
        'content' => 'Belum terbit.',
        'status' => 'draft',
        'show_in_navigation' => true,
    ]);
    $scheduledPage = ContentPage::query()->create([
        'slug' => 'halaman-terjadwal',
        'title' => 'Halaman Terjadwal',
        'template' => 'landing',
        'content' => 'Akan datang.',
        'status' => 'published',
        'show_in_navigation' => true,
        'published_at' => now()->addDay(),
    ]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Lainnya')
        ->assertSee('id="public-more-navigation"', false)
        ->assertSeeInOrder(['data-analytics-event="pricing"', 'aria-controls="public-more-navigation"'], false)
        ->assertSee('Kebijakan Privasi')
        ->assertSee(route('pages.show', $publishedPage), false)
        ->assertDontSee('Informasi Internal')
        ->assertDontSee('Halaman Draft')
        ->assertDontSee('Halaman Terjadwal');

    $this->get(route('pages.show', $publishedPage))
        ->assertSuccessful()
        ->assertSee('<title>Kebijakan Privasi — Naf Dreams</title>', false)
        ->assertSee('<meta name="description" content="Penjelasan pengelolaan data pribadi.">', false)
        ->assertSee('Data yang dikumpulkan')
        ->assertSee('id="data-yang-dikumpulkan"', false);

    $this->get(route('pages.show', $hiddenPage))->assertSuccessful();
    $this->get(route('pages.show', $draftPage))->assertNotFound();
    $this->get(route('pages.show', $scheduledPage))->assertNotFound();
});
