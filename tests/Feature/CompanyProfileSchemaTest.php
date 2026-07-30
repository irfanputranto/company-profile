<?php

use App\Models\ContentTranslation;
use App\Models\Language;
use App\Models\MediaVariant;
use App\Models\PageVisit;
use App\Models\VisitAggregate;
use App\Modules\CompanyProfile\Support\ContentResourceRegistry;
use Database\Seeders\PersonalProfileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('menyediakan skema company profile yang dapat dikustomisasi', function () {
    expect(Schema::hasColumns('profiles', [
        'slug',
        'headline',
        'about',
        'availability_status',
        'logo_media_id',
        'favicon_media_id',
    ]))
        ->toBeTrue()
        ->and(Schema::hasColumns('services', ['slug', 'summary', 'content', 'is_featured']))
        ->toBeTrue()
        ->and(Schema::hasColumns('projects', ['slug', 'client', 'project_url', 'repository_url']))
        ->toBeTrue()
        ->and(Schema::hasColumns('articles', ['slug', 'content', 'status', 'published_at']))
        ->toBeTrue()
        ->and(Schema::hasColumns('seo_metadata', ['meta_title', 'canonical_url', 'structured_data']))
        ->toBeTrue()
        ->and(Schema::hasColumns('visit_aggregates', ['period_type', 'period_start', 'page_views']))
        ->toBeTrue();
});

it('memetakan seluruh model company profile ke tabel migration yang tersedia', function () {
    $models = collect(ContentResourceRegistry::keys())
        ->map(fn (string $resource): string => ContentResourceRegistry::get($resource)['model'])
        ->merge([
            Language::class,
            ContentTranslation::class,
            MediaVariant::class,
            PageVisit::class,
            VisitAggregate::class,
        ])
        ->unique();

    foreach ($models as $model) {
        $table = (new $model)->getTable();

        $this->assertTrue(
            Schema::hasTable($table),
            "Model {$model} mengarah ke tabel {$table} yang tidak tersedia.",
        );
    }
});

it('menyediakan audit pengguna dan soft delete pada seluruh entitas cms', function () {
    $auditedTables = [
        'profiles',
        'social_links',
        'experiences',
        'educations',
        'site_settings',
        'article_categories',
        'articles',
        'tags',
        'content_pages',
        'skill_categories',
        'skills',
        'services',
        'projects',
        'testimonials',
        'faqs',
        'media',
        'seo_metadata',
    ];

    foreach ($auditedTables as $table) {
        $this->assertTrue(
            Schema::hasColumns($table, [
                'created_by',
                'updated_by',
                'deleted_by',
                'created_at',
                'updated_at',
                'deleted_at',
            ]),
            "Tabel {$table} belum memiliki kolom audit lengkap.",
        );
    }
});

it('mengisi profil personal berdasarkan cv Irfan', function () {
    $this->seed(PersonalProfileSeeder::class);

    $this->assertDatabaseHas('profiles', [
        'slug' => 'irfan-putranto-pratama',
        'public_name' => 'Irfan Putranto Pratama',
        'years_experience' => 7,
    ]);
    $this->assertDatabaseCount('experiences', 5);
    $this->assertDatabaseHas('educations', [
        'institution' => 'Universitas Gajayana Malang',
        'grade' => 3.45,
    ]);
    $this->assertDatabaseHas('services', [
        'slug' => 'backend-api-development',
        'is_active' => true,
    ]);
});
