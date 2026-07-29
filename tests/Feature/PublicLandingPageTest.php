<?php

use App\Models\Article;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\Language;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Testimonial;
use App\Models\User;
use App\Modules\CompanyProfile\Services\LanguageResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Language::query()->create([
        'code' => 'en',
        'name' => 'English',
        'native_name' => 'English',
        'direction' => 'ltr',
        'is_default' => true,
        'is_active' => true,
        'sort_order' => 1,
    ]);
    app(LanguageResolver::class)->forget();

    $this->user = User::factory()->create();
    $this->profile = Profile::query()->create([
        'user_id' => $this->user->id,
        'slug' => 'public-engineer',
        'public_name' => 'Public Engineer',
        'headline' => 'Building dependable digital products',
        'short_bio' => 'Backend engineering, integration, and performance.',
        'about' => 'I help teams turn complex product requirements into maintainable software.',
        'email' => 'engineer@example.test',
        'phone' => '+628123456789',
        'location' => 'Jakarta',
        'timezone' => 'Asia/Jakarta',
        'availability_status' => 'available',
        'years_experience' => 8,
        'is_active' => true,
    ]);

    Service::query()->create([
        'profile_id' => $this->profile->id,
        'slug' => 'backend-engineering',
        'title' => 'Backend Engineering',
        'summary' => 'Reliable APIs and scalable application architecture.',
        'content' => 'Architecture, implementation, testing, and performance tuning.',
        'currency' => 'IDR',
        'sort_order' => 1,
        'is_featured' => true,
        'is_active' => true,
    ]);

    $category = SkillCategory::query()->create([
        'name' => 'Backend',
        'slug' => 'backend',
        'sort_order' => 1,
    ]);
    $skill = Skill::query()->create([
        'profile_id' => $this->profile->id,
        'skill_category_id' => $category->id,
        'name' => 'Laravel',
        'slug' => 'laravel',
        'proficiency' => 95,
        'years_experience' => 8,
        'sort_order' => 1,
        'is_featured' => true,
        'is_active' => true,
    ]);

    $project = Project::query()->create([
        'profile_id' => $this->profile->id,
        'slug' => 'scalable-platform',
        'title' => 'Scalable Platform',
        'summary' => 'A high-traffic platform with reliable integrations.',
        'content' => 'Project details.',
        'sort_order' => 1,
        'is_featured' => true,
        'is_active' => true,
    ]);
    $project->skills()->attach($skill);

    Experience::query()->create([
        'profile_id' => $this->profile->id,
        'company' => 'Engineering Company',
        'role' => 'Senior Backend Engineer',
        'started_at' => now()->subYears(2),
        'is_current' => true,
        'summary' => 'Leading backend architecture and delivery.',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    Testimonial::query()->create([
        'profile_id' => $this->profile->id,
        'client_name' => 'Product Lead',
        'client_role' => 'Head of Product',
        'company' => 'Product Company',
        'quote' => 'Dependable delivery with thoughtful technical decisions.',
        'rating' => 5,
        'sort_order' => 1,
        'is_featured' => true,
        'is_active' => true,
    ]);

    Faq::query()->create([
        'profile_id' => $this->profile->id,
        'question' => 'What kind of projects do you take?',
        'answer' => 'Backend, integration, performance, and full-stack product work.',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    SocialLink::query()->create([
        'profile_id' => $this->profile->id,
        'platform' => 'github',
        'label' => 'GitHub',
        'url' => 'https://github.com/example',
        'username' => 'example',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    Article::query()->create([
        'author_id' => $this->user->id,
        'slug' => 'production-laravel',
        'title' => 'Production Laravel',
        'excerpt' => 'Practical lessons from production systems.',
        'content' => 'Article content.',
        'status' => 'published',
        'is_featured' => true,
        'reading_time_minutes' => 6,
        'published_at' => now()->subDay(),
    ]);
});

it('renders the Bigspring-inspired landing page from company profile models without lazy loading', function (): void {
    Model::preventLazyLoading();

    try {
        $this->get(route('home'))
            ->assertSuccessful()
            ->assertSee('Building dependable digital products')
            ->assertSee('Backend Engineering')
            ->assertSee('Laravel')
            ->assertSee('Scalable Platform')
            ->assertSee('Senior Backend Engineer')
            ->assertSee('Dependable delivery with thoughtful technical decisions.')
            ->assertSee('Production Laravel')
            ->assertSee('What kind of projects do you take?')
            ->assertSee('vendor/bigspring/images/banner-art.svg')
            ->assertSee('vendor/bigspring/images/service-slide-1.webp')
            ->assertSee('bigspring-home');
    } finally {
        Model::preventLazyLoading(false);
    }
});

it('keeps the copied Bigspring assets and license available', function (): void {
    expect(public_path('vendor/bigspring/LICENSE'))->toBeFile()
        ->and(public_path('vendor/bigspring/images/banner-art.svg'))->toBeFile()
        ->and(public_path('vendor/bigspring/images/banner.svg'))->toBeFile()
        ->and(public_path('vendor/bigspring/images/cta.svg'))->toBeFile()
        ->and(public_path('vendor/bigspring/images/service-slide-1.webp'))->toBeFile()
        ->and(public_path('vendor/bigspring/images/blog-1.webp'))->toBeFile();
});
