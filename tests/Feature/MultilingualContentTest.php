<?php

use App\Models\Language;
use App\Models\Profile;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use App\Modules\CompanyProfile\Services\LanguageResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->administrator = User::factory()->create([
        'uuid' => (string) Str::uuid(),
        'username' => 'multilingual-admin',
        'is_active' => true,
    ]);
    grantMasterPermissions($this->administrator, 'languages');
    grantMasterPermissions($this->administrator, 'services');
    grantMasterPermissions($this->administrator, 'content_pages');

    $this->indonesian = Language::query()->create([
        'code' => 'id',
        'name' => 'Indonesian',
        'native_name' => 'Bahasa Indonesia',
        'direction' => 'ltr',
        'is_default' => true,
        'is_active' => true,
        'sort_order' => 1,
    ]);
    $this->english = Language::query()->create([
        'code' => 'en',
        'name' => 'English',
        'native_name' => 'English',
        'direction' => 'ltr',
        'is_default' => false,
        'is_active' => true,
        'sort_order' => 2,
    ]);
    app(LanguageResolver::class)->forget();
    Activity::query()->delete();
});

it('menggunakan bahasa default dan mempertahankan pilihan yang sama di web serta admin', function (): void {
    $this->english->update(['is_default' => true]);
    $this->indonesian->update(['is_default' => false]);
    app(LanguageResolver::class)->forget();

    expect(app(LanguageResolver::class)->defaultCode())->toBe('en');

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('<html lang="en"', false)
        ->assertSee('Building fast, secure, and scalable web products.');

    $this->get(route('localized-home', $this->indonesian))
        ->assertSuccessful()
        ->assertSessionHas('site_locale', 'id')
        ->assertSee('<html lang="id"', false)
        ->assertSee('Membangun produk web yang cepat, aman, dan siap berkembang.');

    $this->actingAs($this->administrator)
        ->get(route('company-profile.languages.index'))
        ->assertSuccessful()
        ->assertSee('<html lang="id"', false);
});

it('menerjemahkan shell dan form company profile di admin sesuai bahasa aktif', function (): void {
    $this->withSession(['site_locale' => 'en'])
        ->actingAs($this->administrator)
        ->get(route('company-profile.content.create', ['resource' => 'content-pages']))
        ->assertSuccessful()
        ->assertSee('<html lang="en"', false)
        ->assertSee('Add Page')
        ->assertSee('Complete the Page details below.')
        ->assertSee('Author')
        ->assertSee('Select Author')
        ->assertSee('Show in navigation')
        ->assertSee('Content')
        ->assertSee('Pages')
        ->assertSee('Save')
        ->assertSee('Cancel')
        ->assertDontSee('Tambah Halaman')
        ->assertDontSee('Pilih penulis');

    $this->withSession(['site_locale' => 'id'])
        ->actingAs($this->administrator)
        ->get(route('company-profile.content.create', ['resource' => 'content-pages']))
        ->assertSuccessful()
        ->assertSee('<html lang="id"', false)
        ->assertSee('Tambah Halaman')
        ->assertSee('Lengkapi data Halaman di bawah ini.')
        ->assertSee('Penulis')
        ->assertSee('Pilih Penulis')
        ->assertSee('Tampilkan di navigasi')
        ->assertSee('Simpan')
        ->assertSee('Batal')
        ->assertDontSee('Add Page');
});

it('dapat menambah bahasa dinamis dan menjadikannya default dari admin', function (): void {
    $this->actingAs($this->administrator)->post(route('company-profile.languages.store'), [
        'code' => 'fr',
        'name' => 'French',
        'native_name' => 'Français',
        'direction' => 'ltr',
        'is_default' => 1,
        'is_active' => 1,
        'sort_order' => 3,
    ])->assertRedirect(route('company-profile.languages.index'));

    $french = Language::query()->where('code', 'fr')->firstOrFail();

    expect($french->is_default)->toBeTrue()
        ->and($this->indonesian->refresh()->is_default)->toBeFalse()
        ->and($this->english->refresh()->is_default)->toBeFalse()
        ->and(SiteSetting::query()->where('key', 'site.default_language')->firstOrFail()->value)->toBe('fr');

    expect(app(LanguageResolver::class)->resolve('fr'))->toBe('fr');

    $this->get(route('localized-home', $french))
        ->assertSuccessful()
        ->assertSessionHas('site_locale', 'fr')
        ->assertSee('<html lang="fr"', false);
});

it('menyimpan terjemahan field yang diizinkan dengan fallback dan activity log', function (): void {
    $profile = Profile::query()->create([
        'slug' => 'irfan',
        'public_name' => 'Irfan Putranto',
        'headline' => 'Software Engineer',
        'timezone' => 'Asia/Jakarta',
        'availability_status' => 'available',
        'years_experience' => 7,
        'is_active' => true,
    ]);
    $service = Service::query()->create([
        'profile_id' => $profile->id,
        'slug' => 'backend',
        'title' => 'Pengembangan Backend',
        'summary' => 'Backend yang cepat.',
        'currency' => 'IDR',
        'sort_order' => 1,
        'is_featured' => true,
        'is_active' => true,
    ]);
    Activity::query()->delete();

    $this->actingAs($this->administrator)->put(route('company-profile.translations.update', [
        'resource' => 'services',
        'record' => $service->id,
    ]), [
        'translations' => [
            'id' => [
                'title' => 'Pengembangan Backend',
                'summary' => 'Backend yang cepat.',
                'content' => null,
                'call_to_action_label' => null,
            ],
            'en' => [
                'title' => 'Backend Development',
                'summary' => 'Fast and scalable backend.',
                'content' => null,
                'call_to_action_label' => null,
            ],
        ],
    ])->assertRedirect();

    expect($service->fresh()->translated('title', 'en'))->toBe('Backend Development')
        ->and($service->fresh()->translated('title', 'id'))->toBe('Pengembangan Backend')
        ->and($service->fresh()->translated('content', 'en'))->toBeNull()
        ->and(Activity::query()
            ->whereMorphedTo('subject', $service)
            ->where('event', 'translations_updated')
            ->exists())->toBeTrue();

    $this->get(route('localized-home', $this->english))
        ->assertSuccessful()
        ->assertSee('Backend Development')
        ->assertDontSee('Pengembangan Backend');
});

it('melindungi pengelolaan bahasa dan terjemahan dengan permission', function (): void {
    $user = User::factory()->create([
        'uuid' => (string) Str::uuid(),
        'username' => 'no-language-access',
    ]);

    $this->actingAs($user)
        ->get(route('company-profile.languages.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('company-profile.languages.store'), [])
        ->assertForbidden();
});

it('tidak memberi noindex pada web publik tetapi tetap melindungi admin', function (): void {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertHeaderMissing('X-Robots-Tag');

    $this->actingAs($this->administrator)
        ->get(route('company-profile.languages.index'))
        ->assertSuccessful()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noimageindex, noarchive, nosnippet');
});
