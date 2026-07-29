<?php

namespace Database\Seeders;

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
    }

    /** @param array<string, string|null> $fields */
    private function translate(Profile|Service $model, int $languageId, array $fields): void
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
