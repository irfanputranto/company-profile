<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\PricingPlan;
use App\Models\Profile;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalProfileTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $languageIds = DB::table('languages')->pluck('id', 'code');
        $profile = Profile::query()->where('slug', 'irfan-putranto-pratama')->first();

        if (! $profile || ! $languageIds->has('id') || ! $languageIds->has('en')) {
            return;
        }

        $this->translate($profile, (int) $languageIds['id'], [
            'headline' => $profile->headline,
            'short_bio' => $profile->short_bio,
            'about' => $profile->about,
        ]);
        $this->translate($profile, (int) $languageIds['en'], [
            'headline' => 'Senior Full-Stack & Backend Engineer',
            'short_bio' => 'Software engineer with more than seven years of experience building e-commerce, POS, government, and industrial applications.',
            'about' => 'Focused on scalable backend systems, API integration, database performance, caching, and user experience across government, retail, e-commerce, and pharmaceutical industries.',
        ]);

        $englishServices = [
            'backend-api-development' => [
                'title' => 'Backend & API Development',
                'summary' => 'Laravel or Golang backend development, REST APIs, GraphQL, system integration, and authentication.',
            ],
            'full-stack-web-development' => [
                'title' => 'Full-Stack Web Development',
                'summary' => 'Responsive web applications using Laravel, React, Vue, Next.js, or NestJS.',
            ],
            'performance-scalability' => [
                'title' => 'Performance & Scalability',
                'summary' => 'Database query optimization, Redis caching, queues, and architecture for high-traffic workloads.',
            ],
            'devops-ci-cd' => [
                'title' => 'DevOps & CI/CD',
                'summary' => 'Application containerization and deployment automation through Docker and GitHub Actions.',
            ],
        ];

        Service::query()->whereIn('slug', array_keys($englishServices))->each(
            function (Service $service) use ($languageIds, $englishServices): void {
                $this->translate($service, (int) $languageIds['id'], [
                    'title' => $service->title,
                    'summary' => $service->summary,
                ]);
                $this->translate($service, (int) $languageIds['en'], $englishServices[$service->slug]);
            },
        );

        $englishFeatures = [
            'konsultasi-kebutuhan' => ['title' => 'Clear Requirement Consultation', 'description' => 'Business needs are mapped first so the solution is focused and relevant.'],
            'solusi-custom' => ['title' => 'Tailored Business Solution', 'description' => 'The application follows your business workflow instead of forcing a generic template.'],
            'clean-code' => ['title' => 'Clean Code', 'description' => 'Structured, tested code that stays maintainable as the business grows.'],
            'aman-scalable' => ['title' => 'Secure and Scalable', 'description' => 'Architecture prepared for security, performance, and traffic growth.'],
            'proses-transparan' => ['title' => 'Transparent Process', 'description' => 'Progress, scope, and priorities are communicated openly throughout delivery.'],
            'support-berkelanjutan' => ['title' => 'Ongoing Support', 'description' => 'Post-launch assistance for fixes, optimization, and future development.'],
        ];

        Feature::query()->whereIn('slug', array_keys($englishFeatures))->each(
            function (Feature $feature) use ($languageIds, $englishFeatures): void {
                $this->translate($feature, (int) $languageIds['id'], [
                    'title' => $feature->title,
                    'description' => $feature->description,
                ]);
                $this->translate($feature, (int) $languageIds['en'], $englishFeatures[$feature->slug]);
            },
        );

        $englishPricingPlans = [
            'starter' => [
                'title' => 'Starter',
                'tagline' => 'For focused digital essentials',
                'description' => 'Ideal for a company profile, landing page, or MVP with a focused scope.',
                'call_to_action_label' => 'Discuss this plan',
            ],
            'professional' => [
                'title' => 'Professional',
                'tagline' => 'For business applications built to grow',
                'description' => 'Ideal for dashboards, internal systems, API integrations, e-commerce, and POS.',
                'call_to_action_label' => 'Discuss this plan',
            ],
            'business' => [
                'title' => 'Business',
                'tagline' => 'For complex systems and custom requirements',
                'description' => 'Ideal for multi-module solutions, high traffic, complex integrations, and ongoing support.',
                'call_to_action_label' => 'Discuss this plan',
            ],
        ];

        PricingPlan::query()->whereIn('slug', array_keys($englishPricingPlans))->each(
            function (PricingPlan $pricingPlan) use ($languageIds, $englishPricingPlans): void {
                $this->translate($pricingPlan, (int) $languageIds['id'], [
                    'title' => $pricingPlan->title,
                    'tagline' => $pricingPlan->tagline,
                    'description' => $pricingPlan->description,
                    'call_to_action_label' => $pricingPlan->call_to_action_label,
                ]);
                $this->translate($pricingPlan, (int) $languageIds['en'], $englishPricingPlans[$pricingPlan->slug]);
            },
        );
    }

    /** @param array<string, string|null> $fields */
    private function translate(Feature|PricingPlan|Profile|Service $model, int $languageId, array $fields): void
    {
        foreach ($fields as $field => $value) {
            DB::table('content_translations')->updateOrInsert(
                [
                    'language_id' => $languageId,
                    'translatable_type' => $model::class,
                    'translatable_id' => $model->getKey(),
                    'field' => $field,
                ],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
