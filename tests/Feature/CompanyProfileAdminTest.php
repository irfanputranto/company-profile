<?php

use App\Models\Article;
use App\Models\Feature;
use App\Models\Media;
use App\Models\PricingPlan;
use App\Models\Profile;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use App\Modules\CompanyProfile\Support\ContentResourceRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->administrator = User::factory()->create([
        'uuid' => (string) Str::uuid(),
        'username' => 'company-profile-admin',
        'is_active' => true,
    ]);
    grantMasterPermissions($this->administrator, 'profiles');
    grantMasterPermissions($this->administrator, 'services');
    grantMasterPermissions($this->administrator, 'features');
    grantMasterPermissions($this->administrator, 'pricing_plans');
    grantMasterPermissions($this->administrator, 'articles');
    grantMasterPermissions($this->administrator, 'media');
    $this->administrator->givePermissionTo(Permission::findOrCreate('view_analytics', 'web'));

    $this->actingAs($this->administrator);
    $this->profile = Profile::query()->create([
        'slug' => 'irfan',
        'public_name' => 'Irfan Putranto',
        'headline' => 'Software Engineer',
        'timezone' => 'Asia/Jakarta',
        'availability_status' => 'available',
        'years_experience' => 7,
        'is_active' => true,
    ]);
    Activity::query()->delete();
});

it('menampilkan halaman dan submenu company profile sesuai permission', function () {
    $response = $this->get(route('company-profile.content.index', ['resource' => 'profiles']))
        ->assertSuccessful()
        ->assertSee('Profil Utama')
        ->assertSee('Company Profile')
        ->assertSee('Portfolio')
        ->assertSee('Website')
        ->assertSee('data-navigation-group-toggle', false)
        ->assertSee('aria-controls="company-profile-navigation-group-0"', false)
        ->assertSee(route('company-profile.content.index', ['resource' => 'profiles']), false)
        ->assertSee(route('company-profile.content.index', ['resource' => 'services']), false);

    expect(substr_count($response->getContent(), 'data-navigation-group-toggle'))->toBe(4)
        ->and(substr_count($response->getContent(), 'data-navigation-group-content'))->toBe(4);

    $this->get(route('company-profile.content.create', ['resource' => 'services']))
        ->assertSuccessful()
        ->assertSee('Tambah Layanan');
});

it('merender daftar seluruh resource reusable', function () {
    foreach (ContentResourceRegistry::keys() as $resource) {
        $permissionResource = str($resource)->replace('-', '_')->toString();
        grantMasterPermissions($this->administrator, $permissionResource);

        $this->get(route('company-profile.content.index', ['resource' => $resource]))
            ->assertSuccessful();
    }
});

it('mengatur nama brand logo dan favicon dari profil utama', function () {
    $logo = Media::query()->create([
        'uuid' => (string) Str::uuid(),
        'uploaded_by' => $this->administrator->id,
        'disk' => 'content_media',
        'path' => 'company-profile/media/brand-logo.webp',
        'original_name' => 'brand-logo.png',
        'mime_type' => 'image/webp',
        'extension' => 'webp',
        'byte_size' => 1024,
    ]);
    $favicon = Media::query()->create([
        'uuid' => (string) Str::uuid(),
        'uploaded_by' => $this->administrator->id,
        'disk' => 'content_media',
        'path' => 'company-profile/media/favicon.webp',
        'original_name' => 'favicon.png',
        'mime_type' => 'image/webp',
        'extension' => 'webp',
        'byte_size' => 512,
    ]);

    $this->get(route('company-profile.content.edit', [
        'resource' => 'profiles',
        'record' => $this->profile->id,
    ]))
        ->assertSuccessful()
        ->assertSee('Nama brand')
        ->assertSee('Logo brand (unggah melalui menu Media)')
        ->assertSee('Favicon (unggah melalui menu Media)')
        ->assertSee('brand-logo.png')
        ->assertSee('favicon.png');

    $this->put(route('company-profile.content.update', [
        'resource' => 'profiles',
        'record' => $this->profile->id,
    ]), [
        'public_name' => 'Brand Dinamis',
        'logo_media_id' => $logo->id,
        'favicon_media_id' => $favicon->id,
        'headline' => $this->profile->headline,
        'slug' => $this->profile->slug,
        'email' => null,
        'phone' => null,
        'location' => null,
        'timezone' => $this->profile->timezone,
        'availability_status' => $this->profile->availability_status,
        'years_experience' => $this->profile->years_experience,
        'short_bio' => null,
        'about' => null,
        'is_active' => 1,
    ])->assertSessionHasNoErrors()->assertRedirect();

    $profile = $this->profile->refresh();

    expect($profile->public_name)->toBe('Brand Dinamis')
        ->and($profile->logo_media_id)->toBe($logo->id)
        ->and($profile->favicon_media_id)->toBe($favicon->id);
});

it('menyimpan memperbarui dan menghapus service dengan audit serta activity log', function () {
    $storeResponse = $this->post(route('company-profile.content.store', ['resource' => 'services']), [
        'profile_id' => $this->profile->id,
        'title' => 'Backend Development',
        'slug' => '',
        'summary' => 'Membangun backend Laravel yang aman dan scalable.',
        'content' => 'Detail layanan backend.',
        'icon' => 'server',
        'starting_price' => 5000000,
        'currency' => 'IDR',
        'call_to_action_label' => 'Diskusikan',
        'call_to_action_url' => 'mailto:irfan@example.test',
        'sort_order' => 1,
        'is_featured' => 1,
        'is_active' => 1,
    ]);

    $storeResponse->assertRedirect(route('company-profile.content.index', ['resource' => 'services']));

    $service = Service::query()->where('slug', 'backend-development')->firstOrFail();
    expect($service->created_by)->toBe($this->administrator->id)
        ->and($service->updated_by)->toBe($this->administrator->id);

    $this->put(route('company-profile.content.update', [
        'resource' => 'services',
        'record' => $service->id,
    ]), [
        'profile_id' => $this->profile->id,
        'title' => 'Backend & API Development',
        'slug' => $service->slug,
        'summary' => 'Backend dan API terintegrasi.',
        'content' => null,
        'icon' => null,
        'starting_price' => null,
        'currency' => 'IDR',
        'call_to_action_label' => null,
        'call_to_action_url' => null,
        'sort_order' => 1,
        'is_featured' => 1,
        'is_active' => 1,
    ])->assertRedirect(route('company-profile.content.index', ['resource' => 'services']));

    $this->delete(route('company-profile.content.destroy', [
        'resource' => 'services',
        'record' => $service->id,
    ]))->assertRedirect();

    $service = Service::withTrashed()->findOrFail($service->id);
    expect($service->title)->toBe('Backend & API Development')
        ->and($service->deleted_by)->toBe($this->administrator->id)
        ->and($service->deleted_at)->not->toBeNull();

    $activities = Activity::query()->whereMorphedTo('subject', $service)->get();
    expect($activities->pluck('event')->all())->toBe(['created', 'updated', 'deleted'])
        ->and($activities->every(fn (Activity $activity): bool => $activity->causer_id === $this->administrator->id))->toBeTrue();
});

it('membuat artikel dengan slug dan penulis otomatis', function () {
    $tag = Tag::query()->create(['name' => 'Laravel', 'slug' => 'laravel']);

    $this->post(route('company-profile.content.store', ['resource' => 'articles']), [
        'author_id' => null,
        'article_category_id' => null,
        'title' => 'Optimasi Laravel untuk Trafik Tinggi',
        'slug' => '',
        'excerpt' => 'Strategi caching dan agregasi.',
        'content' => 'Isi artikel.',
        'tag_ids' => [$tag->id],
        'status' => 'draft',
        'is_featured' => 1,
        'reading_time_minutes' => 5,
        'published_at' => null,
    ])->assertRedirect(route('company-profile.content.index', ['resource' => 'articles']));

    $article = Article::query()->firstOrFail();
    expect($article->slug)->toBe('optimasi-laravel-untuk-trafik-tinggi')
        ->and($article->author_id)->toBe($this->administrator->id)
        ->and($article->tags)->toHaveCount(1)
        ->and(Activity::query()->whereMorphedTo('subject', $article)->where('event', 'updated')->exists())
        ->toBeTrue();
});

it('mengelola paket harga dan fitur melalui reusable crud', function () {
    $this->post(route('company-profile.content.store', ['resource' => 'features']), [
        'profile_id' => $this->profile->id,
        'title' => 'Konsultasi kebutuhan',
        'slug' => '',
        'description' => 'Memetakan kebutuhan dan prioritas bisnis.',
        'icon' => 'messages',
        'sort_order' => 1,
        'is_featured' => 1,
        'is_active' => 1,
    ])->assertRedirect();

    $feature = Feature::query()->firstOrFail();

    $this->post(route('company-profile.content.store', ['resource' => 'pricing-plans']), [
        'profile_id' => $this->profile->id,
        'title' => 'Professional',
        'slug' => '',
        'tagline' => 'Untuk bisnis yang siap berkembang',
        'description' => 'Paket pengembangan aplikasi custom.',
        'price' => 7500000,
        'currency' => 'IDR',
        'billing_period' => 'project',
        'call_to_action_label' => 'Konsultasi sekarang',
        'call_to_action_url' => 'mailto:irfan@example.test',
        'feature_ids' => [$feature->id],
        'sort_order' => 2,
        'is_featured' => 1,
        'is_active' => 1,
    ])->assertSessionHasNoErrors()->assertRedirect();

    $pricingPlan = PricingPlan::query()->firstOrFail();

    expect($pricingPlan->slug)->toBe('professional')
        ->and($pricingPlan->features)->toHaveCount(1)
        ->and($pricingPlan->created_by)->toBe($this->administrator->id)
        ->and(Activity::query()->whereMorphedTo('subject', $pricingPlan)->exists())->toBeTrue();
});

it('menolak akses pengguna tanpa permission resource', function () {
    $user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'content-no-access']);

    $this->actingAs($user)
        ->get(route('company-profile.content.index', ['resource' => 'services']))
        ->assertForbidden();
});

it('mengunggah media menjadi webp dan mencatat activity', function () {
    Storage::fake('content_media');

    $this->post(route('company-profile.media.store'), [
        'image' => UploadedFile::fake()->image('project.jpg', 1800, 1200),
        'alt_text' => 'Dashboard proyek Irfan',
        'caption' => 'Tampilan dashboard.',
    ])->assertRedirect(route('company-profile.media.index'));

    $media = Media::query()->firstOrFail();
    expect($media->path)->toEndWith('.webp')
        ->and($media->variants)->toHaveCount(3)
        ->and($media->created_by)->toBe($this->administrator->id);

    Storage::disk('content_media')->assertExists($media->path);
    expect(Activity::query()->whereMorphedTo('subject', $media)->where('event', 'created')->exists())->toBeTrue();
});

it('melindungi halaman analytics dengan permission', function () {
    $this->get(route('company-profile.analytics.index'))
        ->assertSuccessful()
        ->assertSee('Analitik Visit');

    $user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'analytics-no-access']);
    $this->actingAs($user)->get(route('company-profile.analytics.index'))->assertForbidden();
});
