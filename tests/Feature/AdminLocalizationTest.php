<?php

use App\Models\ContentPage;
use App\Models\Language;
use App\Models\Media;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Modules\CompanyProfile\Services\LanguageResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->administrator = User::factory()->create([
        'uuid' => (string) Str::uuid(),
        'username' => 'admin-localization',
        'is_active' => true,
    ]);

    foreach (['users', 'roles', 'permissions', 'languages', 'media', 'content_pages'] as $resource) {
        grantMasterPermissions($this->administrator, $resource);
    }

    $this->administrator->givePermissionTo([
        Permission::findOrCreate('view_activity_logs', 'web'),
        Permission::findOrCreate('view_analytics', 'web'),
    ]);

    $this->english = Language::query()->create([
        'code' => 'en',
        'name' => 'English',
        'native_name' => 'English',
        'direction' => 'ltr',
        'is_default' => true,
        'is_active' => true,
        'sort_order' => 1,
    ]);
    Language::query()->create([
        'code' => 'id',
        'name' => 'Indonesian',
        'native_name' => 'Bahasa Indonesia',
        'direction' => 'ltr',
        'is_default' => false,
        'is_active' => true,
        'sort_order' => 2,
    ]);

    app(LanguageResolver::class)->forget();

    $this->role = Role::findOrCreate('localization_editor', 'web');
    $this->permission = Permission::findOrCreate('localization_custom', 'web');
    $this->contentPage = ContentPage::query()->create([
        'author_id' => $this->administrator->id,
        'slug' => 'localization-page',
        'title' => 'Localization Page',
        'template' => 'default',
        'content' => 'Page content',
        'status' => 'draft',
        'show_in_navigation' => true,
        'sort_order' => 1,
    ]);
    $this->media = Media::query()->create([
        'uuid' => (string) Str::uuid(),
        'uploaded_by' => $this->administrator->id,
        'disk' => 'public',
        'path' => 'company-profile/media/localization.webp',
        'original_name' => 'localization.webp',
        'mime_type' => 'image/webp',
        'extension' => 'webp',
        'byte_size' => 1024,
        'width' => 100,
        'height' => 100,
        'alt_text' => 'Localization image',
    ]);
    $this->activity = Activity::query()->create([
        'log_name' => 'model',
        'description' => 'Updated localization record',
        'event' => 'updated',
        'causer_type' => User::class,
        'causer_id' => $this->administrator->id,
        'subject_type' => ContentPage::class,
        'subject_id' => $this->contentPage->id,
        'properties' => [
            'old' => ['title' => 'Old title'],
            'attributes' => ['title' => 'New title'],
        ],
    ]);
});

it('renders every admin page in English when English is active', function (): void {
    $pages = [
        [route('dashboard'), 'The foundation is ready to grow'],
        [route('profile'), 'Your account information and profile photo.'],
        [route('master.users.index'), 'Manage user accounts and roles.'],
        [route('master.users.create'), 'Complete the user account and access details.'],
        [route('master.users.edit', $this->administrator->uuid), "Update the account for {$this->administrator->name}."],
        [route('master.roles.index'), 'Manage roles and permission sets for each type of user.'],
        [route('master.roles.create'), 'Create a role and select the allowed permissions.'],
        [route('master.roles.edit', $this->role->id), "Reconfigure permissions for the {$this->role->name} role."],
        [route('master.permissions.index'), 'Manage access permissions that can be assigned to roles and users.'],
        [route('master.permissions.create'), 'Create a new access permission for use by roles.'],
        [route('master.permissions.edit', $this->permission->id), "Update the {$this->permission->name} permission."],
        [route('system.activity-logs.index'), 'Detailed history of data created, updated, and deleted by users.'],
        [route('system.activity-logs.show', $this->activity), "Complete information for activity #{$this->activity->id}."],
        [route('company-profile.content.index', 'content-pages'), 'Manage Pages for the company profile website.'],
        [route('company-profile.content.create', 'content-pages'), 'Complete the Page details below.'],
        [route('company-profile.content.edit', ['resource' => 'content-pages', 'record' => $this->contentPage->id]), 'Update the selected Page details.'],
        [route('company-profile.languages.index'), 'Manage website languages and choose the default language for both the website and admin.'],
        [route('company-profile.languages.create'), 'Add language'],
        [route('company-profile.languages.edit', $this->english), 'Edit language'],
        [route('company-profile.media.index'), 'Manage public images, responsive WebP variants, and SEO alt text.'],
        [route('company-profile.media.create'), 'Images are automatically converted into responsive WebP variants.'],
        [route('company-profile.media.edit', $this->media->uuid), 'Update alt text and caption without changing the image file.'],
        [route('company-profile.translations.edit', ['resource' => 'content-pages', 'record' => $this->contentPage->id]), 'Enter content for each language. Empty fields automatically use default-language content.'],
        [route('company-profile.analytics.index'), 'A ready-to-read summary without counting millions of raw visits when the page opens.'],
    ];

    foreach ($pages as [$url, $sentinel]) {
        $this->withSession(['site_locale' => 'en'])
            ->actingAs($this->administrator)
            ->get($url)
            ->assertSuccessful()
            ->assertSee('<html lang="en"', false)
            ->assertSee($sentinel)
            ->assertDontSee('Kelola akun')
            ->assertDontSee('Batal');
    }
});

it('renders shared admin controls and guides in English', function (): void {
    $this->withSession(['site_locale' => 'en'])
        ->actingAs($this->administrator)
        ->get(route('master.users.index'))
        ->assertSuccessful()
        ->assertSee('Data Filters')
        ->assertSee('Search options...')
        ->assertSee('User Guide')
        ->assertSee('How to use this page')
        ->assertSee('Close Guide')
        ->assertDontSee('Filter Data')
        ->assertDontSee('Panduan Pengguna');
});

it('renders the admin login page in English', function (): void {
    $this->withSession(['site_locale' => 'en'])
        ->get(route('login'))
        ->assertSuccessful()
        ->assertSee('<html lang="en"', false)
        ->assertSee('Sign in to your account')
        ->assertSee('Remember me on this device')
        ->assertDontSee('Masuk ke akun');
});
