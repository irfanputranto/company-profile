<?php

use App\Models\Article;
use App\Models\Feature;
use App\Models\Language;
use App\Models\PricingPlan;
use App\Models\Profile;
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
        ->assertSee(route('blog.index'), false)
        ->assertSee(route('pricing.index'), false)
        ->assertSee('Beranda')
        ->assertSee('Blog')
        ->assertSee('Harga');

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
    $publishedArticle = Article::query()->create([
        'author_id' => $this->user->id,
        'slug' => 'membangun-aplikasi-bisnis',
        'title' => 'Membangun Aplikasi Bisnis',
        'excerpt' => 'Panduan memulai aplikasi custom.',
        'content' => 'Mulai dari masalah bisnis dan hasil yang ingin dicapai.',
        'status' => 'published',
        'reading_time_minutes' => 5,
        'published_at' => now()->subDay(),
    ]);
    $draftArticle = Article::query()->create([
        'author_id' => $this->user->id,
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
        ->assertSee('Mulai dari masalah bisnis');

    $this->get(route('blog.show', $draftArticle))->assertNotFound();
});
