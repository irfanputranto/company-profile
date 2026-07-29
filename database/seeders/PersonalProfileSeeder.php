<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalProfileSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $userId = DB::table('users')->where('email', 'admin@example.test')->value('id');

        DB::table('profiles')->updateOrInsert(
            ['slug' => 'irfan-putranto-pratama'],
            [
                'user_id' => $userId,
                'public_name' => 'Irfan Putranto Pratama',
                'headline' => 'Senior Full-Stack & Backend Engineer',
                'short_bio' => 'Software engineer dengan pengalaman lebih dari 7 tahun membangun e-commerce, POS, sistem pemerintahan, dan aplikasi industri.',
                'about' => 'Berfokus pada backend yang scalable, integrasi API, performa database, caching, dan pengalaman pengguna. Berpengalaman membangun solusi untuk pemerintahan, retail, e-commerce, dan industri farmasi.',
                'email' => 'pratamairfanputranto@gmail.com',
                'phone' => '+6289680641487',
                'location' => 'Jakarta, Indonesia',
                'timezone' => 'Asia/Jakarta',
                'availability_status' => 'available',
                'years_experience' => 7,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $profileId = (int) DB::table('profiles')
            ->where('slug', 'irfan-putranto-pratama')
            ->value('id');

        DB::table('social_links')->updateOrInsert(
            ['profile_id' => $profileId, 'platform' => 'github'],
            [
                'label' => 'GitHub',
                'url' => 'https://github.com/irfanputranto',
                'username' => 'irfanputranto',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $this->seedExperiences($profileId, $now);
        $this->seedEducation($profileId, $now);
        $this->seedSkills($profileId, $now);
        $this->seedServices($profileId, $now);
        $this->seedSettings($now);
    }

    private function seedExperiences(int $profileId, mixed $now): void
    {
        $experiences = [
            [
                'company' => 'Dinas Komunikasi dan Informatika',
                'role' => 'Programmer',
                'location' => 'Jombang, Indonesia',
                'started_at' => '2024-08-01',
                'ended_at' => null,
                'is_current' => true,
                'summary' => 'Mengembangkan aplikasi pemerintahan end-to-end, dari React dan Tailwind CSS hingga API Golang dan backend Laravel.',
                'highlights' => ['CI/CD dengan GitHub Actions', 'Caching Redis untuk high traffic', 'MySQL dan PostgreSQL', 'Automated testing'],
                'technologies' => ['React.js', 'Tailwind CSS', 'Golang', 'Laravel', 'MySQL', 'PostgreSQL', 'Redis', 'GitHub Actions'],
            ],
            [
                'company' => '62 Teknologi',
                'role' => 'Full-Stack Developer',
                'location' => 'Jakarta, Indonesia',
                'started_at' => '2024-02-01',
                'ended_at' => '2024-06-30',
                'is_current' => false,
                'summary' => 'Membangun frontend responsif dan API backend serta berkolaborasi dengan tim desain dan QA.',
                'highlights' => ['Prototyping bersama tim desain', 'Pengembangan API Laravel', 'Kolaborasi QA'],
                'technologies' => ['Laravel', 'React.js', 'Tailwind CSS', 'NestJS', 'MySQL', 'PostgreSQL', 'Jira'],
            ],
            [
                'company' => 'MiTech',
                'role' => 'Programmer',
                'location' => 'Jakarta, Indonesia',
                'started_at' => '2023-06-01',
                'ended_at' => '2023-12-31',
                'is_current' => false,
                'summary' => 'Membangun sistem inventori Kementerian Pertanian dengan arsitektur microservices.',
                'highlights' => ['Microservices', 'Single Sign-On', 'Telegram Bot'],
                'technologies' => ['Vue', 'Lumen', 'Docker', 'PHP', 'Redis', 'Bootstrap'],
            ],
            [
                'company' => 'Icube by SIRCLO',
                'role' => 'Backend Engineer',
                'location' => 'Yogyakarta, Indonesia',
                'started_at' => '2021-01-01',
                'ended_at' => '2023-06-30',
                'is_current' => false,
                'summary' => 'Mengembangkan backend e-commerce yang scalable dan membimbing engineer junior.',
                'highlights' => ['Integrasi multi-database', 'Scalable backend architecture', 'Mentoring junior'],
                'technologies' => ['Magento', 'PHP', 'PHTML', 'Docker', 'MySQL', 'XML', 'GraphQL', 'API'],
            ],
            [
                'company' => 'Informent',
                'role' => 'Full-Stack Web Developer',
                'location' => 'Malang, Indonesia',
                'started_at' => '2019-02-01',
                'ended_at' => '2021-01-31',
                'is_current' => false,
                'summary' => 'Membangun dashboard analitik terintegrasi POS serta mengoptimalkan performa aplikasi retail.',
                'highlights' => ['Dashboard analitik POS', 'Optimasi caching', 'Informasi penjualan dan stok real-time'],
                'technologies' => ['PHP', 'Laravel', 'CodeIgniter', 'JavaScript', 'MySQL', 'HTML', 'CSS'],
            ],
        ];

        foreach ($experiences as $sortOrder => $experience) {
            DB::table('experiences')->updateOrInsert(
                [
                    'profile_id' => $profileId,
                    'company' => $experience['company'],
                    'role' => $experience['role'],
                    'started_at' => $experience['started_at'],
                ],
                [
                    ...$experience,
                    'highlights' => json_encode($experience['highlights'], JSON_THROW_ON_ERROR),
                    'technologies' => json_encode($experience['technologies'], JSON_THROW_ON_ERROR),
                    'sort_order' => $sortOrder + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }

    private function seedEducation(int $profileId, mixed $now): void
    {
        DB::table('educations')->updateOrInsert(
            ['profile_id' => $profileId, 'institution' => 'Universitas Gajayana Malang'],
            [
                'degree' => 'Sarjana',
                'field_of_study' => 'Sistem Informasi',
                'location' => 'Malang, Indonesia',
                'started_at' => '2017-09-01',
                'ended_at' => '2023-02-28',
                'grade' => 3.45,
                'grade_scale' => 4.00,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );
    }

    private function seedSkills(int $profileId, mixed $now): void
    {
        $categories = [
            'programming' => ['name' => 'Programming', 'skills' => ['PHP', 'JavaScript', 'Golang']],
            'framework' => ['name' => 'Framework', 'skills' => ['Laravel', 'CodeIgniter', 'Magento', 'Lumen', 'Vue', 'React.js', 'NestJS', 'Next.js']],
            'database' => ['name' => 'Database', 'skills' => ['MySQL', 'PostgreSQL', 'Redis']],
            'platform' => ['name' => 'Platform & Integration', 'skills' => ['Docker', 'REST API', 'GraphQL', 'GitHub Actions']],
        ];
        $sortOrder = 1;

        foreach ($categories as $categorySlug => $category) {
            DB::table('skill_categories')->updateOrInsert(
                ['slug' => $categorySlug],
                [
                    'name' => $category['name'],
                    'sort_order' => $sortOrder,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
            $categoryId = DB::table('skill_categories')->where('slug', $categorySlug)->value('id');

            foreach ($category['skills'] as $skillName) {
                $slug = str($skillName)->slug()->toString();
                DB::table('skills')->updateOrInsert(
                    ['profile_id' => $profileId, 'slug' => $slug],
                    [
                        'skill_category_id' => $categoryId,
                        'name' => $skillName,
                        'sort_order' => $sortOrder++,
                        'is_featured' => in_array($skillName, ['PHP', 'Laravel', 'Golang', 'MySQL', 'PostgreSQL', 'Redis'], true),
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                );
            }
        }
    }

    private function seedServices(int $profileId, mixed $now): void
    {
        $services = [
            ['backend-api-development', 'Backend & API Development', 'Pengembangan backend Laravel atau Golang, REST API, GraphQL, integrasi sistem, dan autentikasi.', 1],
            ['full-stack-web-development', 'Full-Stack Web Development', 'Aplikasi web responsif menggunakan Laravel, React, Vue, Next.js, atau NestJS.', 2],
            ['performance-scalability', 'Performance & Scalability', 'Optimasi query database, Redis caching, queue, dan arsitektur untuk trafik tinggi.', 3],
            ['devops-ci-cd', 'DevOps & CI/CD', 'Dockerisasi aplikasi dan otomatisasi deployment melalui GitHub Actions.', 4],
        ];

        foreach ($services as [$slug, $title, $summary, $sortOrder]) {
            DB::table('services')->updateOrInsert(
                ['slug' => $slug],
                [
                    'profile_id' => $profileId,
                    'title' => $title,
                    'summary' => $summary,
                    'currency' => 'IDR',
                    'sort_order' => $sortOrder,
                    'is_featured' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }

    private function seedSettings(mixed $now): void
    {
        $settings = [
            ['site.name', 'general', 'string', 'Irfan Putranto Pratama'],
            ['site.tagline', 'general', 'string', 'Senior Full-Stack & Backend Engineer'],
            ['hero.primary_cta', 'hero', 'json', ['label' => 'Diskusikan Proyek', 'url' => 'mailto:pratamairfanputranto@gmail.com']],
            ['seo.default_title', 'seo', 'string', 'Irfan Putranto Pratama — Software Engineer'],
            ['seo.default_description', 'seo', 'string', 'Portfolio pribadi Irfan Putranto Pratama, software engineer berpengalaman di backend, full-stack, database, dan sistem scalable.'],
        ];

        foreach ($settings as [$key, $group, $type, $value]) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'group' => $group,
                    'type' => $type,
                    'value' => json_encode($value, JSON_THROW_ON_ERROR),
                    'is_public' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
