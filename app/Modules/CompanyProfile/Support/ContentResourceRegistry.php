<?php

namespace App\Modules\CompanyProfile\Support;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ContentPage;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\Media;
use App\Models\PricingPlan;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SeoMetadata;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class ContentResourceRegistry
{
    /** @return list<string> */
    public static function keys(): array
    {
        return array_keys(self::resources());
    }

    public static function routeConstraint(): string
    {
        return implode('|', self::keys());
    }

    /** @return array<string, mixed> */
    public static function get(string $resource): array
    {
        $definition = self::resources()[$resource] ?? abort(404);
        $definition['title'] = __("admin.resources.{$resource}.title");
        $definition['singular'] = __("admin.resources.{$resource}.singular");
        $definition['fields'] = collect($definition['fields'])
            ->map(function (array $field): array {
                $field['label'] = __("admin.fields.{$field['name']}");

                if (! isset($field['options'])) {
                    return $field;
                }

                $field['options'] = collect($field['options'])
                    ->map(function (string $label, string|int $value) use ($field): string {
                        $key = "admin.options.{$field['name']}.{$value}";

                        return Lang::has($key) ? __($key) : $label;
                    })
                    ->all();

                return $field;
            })
            ->all();

        return $definition;
    }

    /** @return array<string, mixed> */
    public static function formData(string $resource): array
    {
        $definition = self::get($resource);
        $definition['fields'] = collect($definition['fields'])
            ->map(function (array $field): array {
                if (isset($field['source'])) {
                    [$model, $label] = $field['source'];
                    $field['options'] = $model::query()
                        ->orderBy($label)
                        ->pluck($label, 'id')
                        ->all();
                }

                return $field;
            })
            ->all();

        return $definition;
    }

    /** @return array<string, array<int, mixed>> */
    public static function rules(string $resource, ?int $recordId = null): array
    {
        $definition = self::get($resource);
        $table = (new $definition['model'])->getTable();

        return collect($definition['fields'])
            ->mapWithKeys(function (array $field) use ($recordId, $table): array {
                $rules = $field['rules'];

                if (isset($field['unique'])) {
                    $rules[] = Rule::unique($table, $field['unique'])->ignore($recordId);
                }

                if (isset($field['source']) && $field['type'] !== 'multiselect') {
                    [$model] = $field['source'];
                    $rules[] = Rule::exists((new $model)->getTable(), 'id');
                }

                $fieldRules = [$field['name'] => $rules];

                if (isset($field['source']) && $field['type'] === 'multiselect') {
                    [$model] = $field['source'];
                    $fieldRules[$field['name'].'.*'] = [
                        'integer',
                        Rule::exists((new $model)->getTable(), 'id'),
                    ];
                }

                return $fieldRules;
            })
            ->all();
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function normalize(string $resource, array $data): array
    {
        foreach (self::get($resource)['fields'] as $field) {
            if ($field['type'] !== 'json' || ! array_key_exists($field['name'], $data)) {
                continue;
            }

            $data[$field['name']] = filled($data[$field['name']])
                ? json_decode($data[$field['name']], true, flags: JSON_THROW_ON_ERROR)
                : null;
        }

        return $data;
    }

    /** @return array<string, array<string, mixed>> */
    private static function resources(): array
    {
        $active = self::checkbox('is_active', 'Aktif', list: true);
        $featured = self::checkbox('is_featured', 'Unggulan', list: true);
        $profile = self::select('profile_id', 'Profil', Profile::class, 'public_name', ['required', 'integer'], list: false);
        $sortOrder = self::number('sort_order', 'Urutan', ['required', 'integer', 'min:0', 'max:65535']);
        $publicationStatus = ['draft' => 'Draft', 'published' => 'Terbit', 'archived' => 'Arsip'];

        return [
            'profiles' => self::resource(
                Profile::class,
                'Profil Utama',
                'Profil',
                'tabler--id',
                ['public_name', 'headline', 'email', 'location'],
                [
                    self::text('public_name', 'Nama brand', ['required', 'string', 'max:255'], list: true),
                    self::select('logo_media_id', 'Logo brand', Media::class, 'original_name', ['nullable', 'integer']),
                    self::select('favicon_media_id', 'Favicon', Media::class, 'original_name', ['nullable', 'integer']),
                    self::text('headline', 'Headline', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::email('email', 'Email', ['nullable', 'email:rfc', 'max:255'], list: true),
                    self::text('phone', 'Telepon', ['nullable', 'string', 'max:30']),
                    self::text('location', 'Lokasi', ['nullable', 'string', 'max:255'], list: true),
                    self::text('timezone', 'Zona waktu', ['required', 'timezone', 'max:50']),
                    self::selectChoices('availability_status', 'Status ketersediaan', [
                        'available' => 'Tersedia',
                        'limited' => 'Terbatas',
                        'unavailable' => 'Tidak tersedia',
                    ], ['required', 'string'], list: true),
                    self::number('years_experience', 'Tahun pengalaman', ['required', 'integer', 'min:0', 'max:99']),
                    self::textarea('short_bio', 'Bio singkat', ['nullable', 'string', 'max:1000']),
                    self::textarea('about', 'Tentang saya', ['nullable', 'string']),
                    $active,
                ],
                slugSource: 'public_name',
            ),
            'social-links' => self::resource(
                SocialLink::class,
                'Tautan Sosial',
                'Tautan sosial',
                'tabler--share',
                ['platform', 'label', 'username', 'url'],
                [
                    $profile,
                    self::text('platform', 'Platform', ['required', 'string', 'max:50'], list: true),
                    self::text('label', 'Label', ['required', 'string', 'max:255'], list: true),
                    self::text('url', 'URL', ['required', 'url:http,https', 'max:2048'], list: true),
                    self::text('username', 'Username', ['nullable', 'string', 'max:255']),
                    $sortOrder,
                    $active,
                ],
            ),
            'experiences' => self::resource(
                Experience::class,
                'Pengalaman Kerja',
                'Pengalaman',
                'tabler--briefcase',
                ['company', 'role', 'location', 'summary'],
                [
                    $profile,
                    self::text('company', 'Perusahaan', ['required', 'string', 'max:255'], list: true),
                    self::text('role', 'Posisi', ['required', 'string', 'max:255'], list: true),
                    self::text('location', 'Lokasi', ['nullable', 'string', 'max:255'], list: true),
                    self::text('employment_type', 'Jenis pekerjaan', ['nullable', 'string', 'max:50']),
                    self::date('started_at', 'Mulai', ['required', 'date'], list: true),
                    self::date('ended_at', 'Selesai', ['nullable', 'date', 'after_or_equal:started_at']),
                    self::checkbox('is_current', 'Masih bekerja di sini'),
                    self::textarea('summary', 'Ringkasan', ['nullable', 'string']),
                    self::json('highlights', 'Highlights JSON'),
                    self::json('technologies', 'Teknologi JSON'),
                    $sortOrder,
                    $active,
                ],
                order: 'started_at',
            ),
            'educations' => self::resource(
                Education::class,
                'Pendidikan',
                'Pendidikan',
                'tabler--school',
                ['institution', 'degree', 'field_of_study', 'location'],
                [
                    $profile,
                    self::text('institution', 'Institusi', ['required', 'string', 'max:255'], list: true),
                    self::text('degree', 'Gelar', ['required', 'string', 'max:255'], list: true),
                    self::text('field_of_study', 'Bidang studi', ['nullable', 'string', 'max:255'], list: true),
                    self::text('location', 'Lokasi', ['nullable', 'string', 'max:255']),
                    self::date('started_at', 'Mulai', ['nullable', 'date']),
                    self::date('ended_at', 'Selesai', ['nullable', 'date', 'after_or_equal:started_at']),
                    self::number('grade', 'Nilai', ['nullable', 'numeric', 'min:0', 'max:9.99'], step: '0.01'),
                    self::number('grade_scale', 'Skala nilai', ['nullable', 'numeric', 'min:0', 'max:9.99'], step: '0.01'),
                    self::textarea('description', 'Deskripsi', ['nullable', 'string']),
                    $sortOrder,
                    $active,
                ],
            ),
            'skill-categories' => self::resource(
                SkillCategory::class,
                'Kategori Skill',
                'Kategori skill',
                'tabler--category',
                ['name', 'slug'],
                [
                    self::text('name', 'Nama kategori', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    $sortOrder,
                ],
                slugSource: 'name',
            ),
            'skills' => self::resource(
                Skill::class,
                'Skill',
                'Skill',
                'tabler--code',
                ['name', 'slug'],
                [
                    $profile,
                    self::select('skill_category_id', 'Kategori', SkillCategory::class, 'name', ['nullable', 'integer'], list: false),
                    self::text('name', 'Nama skill', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255']),
                    self::number('proficiency', 'Kemahiran (%)', ['nullable', 'integer', 'min:0', 'max:100'], list: true),
                    self::number('years_experience', 'Tahun pengalaman', ['nullable', 'integer', 'min:0', 'max:99']),
                    $sortOrder,
                    $featured,
                    $active,
                ],
                slugSource: 'name',
            ),
            'services' => self::resource(
                Service::class,
                'Layanan',
                'Layanan',
                'tabler--tools',
                ['title', 'slug', 'summary'],
                [
                    $profile,
                    self::text('title', 'Judul', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::textarea('summary', 'Ringkasan', ['required', 'string', 'max:2000'], list: true),
                    self::textarea('content', 'Konten', ['nullable', 'string']),
                    self::text('icon', 'Nama icon', ['nullable', 'string', 'max:255']),
                    self::number('starting_price', 'Harga mulai', ['nullable', 'numeric', 'min:0'], step: '0.01'),
                    self::text('currency', 'Mata uang', ['required', 'string', 'size:3']),
                    self::text('call_to_action_label', 'Label CTA', ['nullable', 'string', 'max:255']),
                    self::text('call_to_action_url', 'URL CTA', ['nullable', 'string', 'max:2048']),
                    $sortOrder,
                    $featured,
                    $active,
                ],
                slugSource: 'title',
            ),
            'features' => self::resource(
                Feature::class,
                'Keunggulan',
                'Keunggulan',
                'tabler--sparkles',
                ['title', 'slug', 'description'],
                [
                    $profile,
                    self::text('title', 'Judul', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::textarea('description', 'Deskripsi', ['required', 'string', 'max:2000'], list: true),
                    self::text('icon', 'Nama icon', ['nullable', 'string', 'max:255']),
                    $sortOrder,
                    $featured,
                    $active,
                ],
                slugSource: 'title',
            ),
            'pricing-plans' => self::resource(
                PricingPlan::class,
                'Paket Harga',
                'Paket harga',
                'tabler--receipt-2',
                ['title', 'slug', 'tagline', 'description'],
                [
                    $profile,
                    self::text('title', 'Judul', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::text('tagline', 'Tagline', ['nullable', 'string', 'max:255'], list: true),
                    self::textarea('description', 'Deskripsi', ['nullable', 'string', 'max:2000']),
                    self::number('price', 'Harga', ['nullable', 'numeric', 'min:0'], list: true, step: '0.01'),
                    self::text('currency', 'Mata uang', ['required', 'string', 'size:3']),
                    self::selectChoices('billing_period', 'Periode harga', [
                        'project' => 'Per proyek',
                        'month' => 'Per bulan',
                        'year' => 'Per tahun',
                    ], ['required', 'string'], list: true),
                    self::text('call_to_action_label', 'Label CTA', ['nullable', 'string', 'max:255']),
                    self::text('call_to_action_url', 'URL CTA', ['nullable', 'string', 'max:2048']),
                    self::multiselect('feature_ids', 'Keunggulan paket', Feature::class, 'title', 'features'),
                    $sortOrder,
                    $featured,
                    $active,
                ],
                slugSource: 'title',
            ),
            'projects' => self::resource(
                Project::class,
                'Proyek',
                'Proyek',
                'tabler--folders',
                ['title', 'slug', 'client', 'summary'],
                [
                    $profile,
                    self::select('service_id', 'Layanan terkait', Service::class, 'title', ['nullable', 'integer'], list: false),
                    self::text('title', 'Judul', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::text('client', 'Klien', ['nullable', 'string', 'max:255'], list: true),
                    self::textarea('summary', 'Ringkasan', ['required', 'string', 'max:2000']),
                    self::textarea('content', 'Konten', ['nullable', 'string']),
                    self::text('project_url', 'URL proyek', ['nullable', 'url:http,https', 'max:2048']),
                    self::text('repository_url', 'URL repository', ['nullable', 'url:http,https', 'max:2048']),
                    self::multiselect('skill_ids', 'Skill proyek', Skill::class, 'name', 'skills'),
                    self::date('started_at', 'Mulai', ['nullable', 'date']),
                    self::date('completed_at', 'Selesai', ['nullable', 'date', 'after_or_equal:started_at']),
                    $sortOrder,
                    $featured,
                    $active,
                ],
                slugSource: 'title',
            ),
            'testimonials' => self::resource(
                Testimonial::class,
                'Testimoni',
                'Testimoni',
                'tabler--message-star',
                ['client_name', 'client_role', 'company', 'quote'],
                [
                    $profile,
                    self::text('client_name', 'Nama klien', ['required', 'string', 'max:255'], list: true),
                    self::text('client_role', 'Posisi klien', ['nullable', 'string', 'max:255'], list: true),
                    self::text('company', 'Perusahaan', ['nullable', 'string', 'max:255']),
                    self::textarea('quote', 'Testimoni', ['required', 'string'], list: true),
                    self::number('rating', 'Rating', ['nullable', 'integer', 'min:1', 'max:5'], list: true),
                    $sortOrder,
                    $featured,
                    $active,
                ],
            ),
            'faqs' => self::resource(
                Faq::class,
                'FAQ',
                'FAQ',
                'tabler--help-circle',
                ['question', 'answer'],
                [
                    $profile,
                    self::text('question', 'Pertanyaan', ['required', 'string', 'max:255'], list: true),
                    self::textarea('answer', 'Jawaban', ['required', 'string'], list: true),
                    $sortOrder,
                    $active,
                ],
            ),
            'content-pages' => self::resource(
                ContentPage::class,
                'Halaman',
                'Halaman',
                'tabler--file-text',
                ['title', 'slug', 'template', 'status'],
                [
                    self::select('author_id', 'Penulis', User::class, 'name', ['nullable', 'integer'], list: false),
                    self::text('title', 'Judul', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::selectChoices('template', 'Template', [
                        'default' => 'Halaman standar',
                        'legal' => 'Dokumen legal',
                        'landing' => 'Landing page',
                    ], ['required', 'string'], list: true),
                    self::textarea('content', 'Konten', ['nullable', 'string']),
                    self::selectChoices('status', 'Status', $publicationStatus, ['required', 'string'], list: true),
                    self::checkbox('show_in_navigation', 'Tampilkan di navigasi', list: true),
                    $sortOrder,
                    self::datetime('published_at', 'Waktu terbit', ['nullable', 'date']),
                ],
                slugSource: 'title',
            ),
            'article-categories' => self::resource(
                ArticleCategory::class,
                'Kategori Artikel',
                'Kategori artikel',
                'tabler--category-2',
                ['name', 'slug', 'description'],
                [
                    self::text('name', 'Nama kategori', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::textarea('description', 'Deskripsi', ['nullable', 'string'], list: true),
                ],
                slugSource: 'name',
            ),
            'articles' => self::resource(
                Article::class,
                'Artikel',
                'Artikel',
                'tabler--news',
                ['title', 'slug', 'excerpt', 'status'],
                [
                    self::select('author_id', 'Penulis', User::class, 'name', ['nullable', 'integer'], list: false),
                    self::select('article_category_id', 'Kategori', ArticleCategory::class, 'name', ['nullable', 'integer'], list: false),
                    self::text('title', 'Judul', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                    self::textarea('excerpt', 'Ringkasan', ['nullable', 'string', 'max:2000'], list: true),
                    self::textarea('content', 'Konten', ['required', 'string']),
                    self::multiselect('tag_ids', 'Tag artikel', Tag::class, 'name', 'tags'),
                    self::selectChoices('status', 'Status', $publicationStatus, ['required', 'string'], list: true),
                    $featured,
                    self::number('reading_time_minutes', 'Waktu baca (menit)', ['nullable', 'integer', 'min:1', 'max:65535']),
                    self::datetime('published_at', 'Waktu terbit', ['nullable', 'date']),
                ],
                slugSource: 'title',
            ),
            'tags' => self::resource(
                Tag::class,
                'Tag Artikel',
                'Tag',
                'tabler--tags',
                ['name', 'slug'],
                [
                    self::text('name', 'Nama tag', ['required', 'string', 'max:255'], list: true),
                    self::text('slug', 'Slug', ['nullable', 'string', 'max:255'], unique: 'slug'),
                ],
                slugSource: 'name',
            ),
            'site-settings' => self::resource(
                SiteSetting::class,
                'Pengaturan Website',
                'Pengaturan',
                'tabler--settings',
                ['key', 'group', 'type'],
                [
                    self::text('key', 'Key', ['required', 'string', 'max:255'], list: true, unique: 'key'),
                    self::text('group', 'Grup', ['required', 'string', 'max:255'], list: true),
                    self::selectChoices('type', 'Tipe', [
                        'string' => 'String',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ], ['required', 'string'], list: true),
                    self::json('value', 'Value JSON', ['nullable', 'json']),
                    self::checkbox('is_public', 'Dapat dibaca publik', list: true),
                ],
            ),
            'seo-metadata' => self::resource(
                SeoMetadata::class,
                'SEO Metadata',
                'SEO metadata',
                'tabler--seo',
                ['seoable_type', 'seoable_id', 'meta_title', 'canonical_url'],
                [
                    self::selectChoices('seoable_type', 'Jenis konten', [
                        Profile::class => 'Profil',
                        Service::class => 'Layanan',
                        Feature::class => 'Keunggulan',
                        PricingPlan::class => 'Paket harga',
                        Project::class => 'Proyek',
                        Article::class => 'Artikel',
                        ContentPage::class => 'Halaman',
                    ], ['required', 'string'], list: true),
                    self::number('seoable_id', 'ID konten', ['required', 'integer', 'min:1'], list: true),
                    self::text('meta_title', 'Meta title', ['nullable', 'string', 'max:255'], list: true),
                    self::textarea('meta_description', 'Meta description', ['nullable', 'string', 'max:500']),
                    self::text('canonical_url', 'Canonical URL', ['nullable', 'url:http,https', 'max:2048']),
                    self::checkbox('robots_index', 'Izinkan indexing'),
                    self::checkbox('robots_follow', 'Izinkan mengikuti link'),
                    self::text('open_graph_title', 'Open Graph title', ['nullable', 'string', 'max:255']),
                    self::textarea('open_graph_description', 'Open Graph description', ['nullable', 'string', 'max:500']),
                    self::select('open_graph_media_id', 'Gambar Open Graph', Media::class, 'original_name', ['nullable', 'integer'], list: false),
                    self::selectChoices('twitter_card', 'Twitter card', [
                        'summary' => 'Summary',
                        'summary_large_image' => 'Summary large image',
                    ], ['required', 'string']),
                    self::json('structured_data', 'Structured data JSON'),
                ],
                order: 'updated_at',
            ),
        ];
    }

    /** @param class-string<Model> $model
     * @param  list<string>  $searchable
     * @param  list<array<string, mixed>>  $fields
     * @return array<string, mixed>
     */
    private static function resource(
        string $model,
        string $title,
        string $singular,
        string $icon,
        array $searchable,
        array $fields,
        string $order = 'id',
        ?string $slugSource = null,
    ): array {
        return compact('model', 'title', 'singular', 'icon', 'searchable', 'fields', 'order', 'slugSource');
    }

    /** @param array<int, mixed> $rules
     * @return array<string, mixed>
     */
    private static function text(
        string $name,
        string $label,
        array $rules,
        bool $list = false,
        ?string $unique = null,
    ): array {
        return self::field($name, $label, 'text', $rules, $list, unique: $unique);
    }

    /** @param array<int, mixed> $rules
     * @return array<string, mixed>
     */
    private static function email(string $name, string $label, array $rules, bool $list = false): array
    {
        return self::field($name, $label, 'email', $rules, $list);
    }

    /** @param array<int, mixed> $rules
     * @return array<string, mixed>
     */
    private static function textarea(string $name, string $label, array $rules, bool $list = false): array
    {
        return self::field($name, $label, 'textarea', $rules, $list, wide: true);
    }

    /** @param array<int, mixed> $rules
     * @return array<string, mixed>
     */
    private static function json(
        string $name,
        string $label,
        array $rules = ['nullable', 'json'],
    ): array {
        return self::field($name, $label, 'json', $rules, wide: true);
    }

    /** @param array<int, mixed> $rules
     * @return array<string, mixed>
     */
    private static function number(
        string $name,
        string $label,
        array $rules,
        bool $list = false,
        string $step = '1',
    ): array {
        return self::field($name, $label, 'number', $rules, $list, extra: ['step' => $step]);
    }

    /** @param array<int, mixed> $rules
     * @return array<string, mixed>
     */
    private static function date(string $name, string $label, array $rules, bool $list = false): array
    {
        return self::field($name, $label, 'date', $rules, $list);
    }

    /** @param array<int, mixed> $rules
     * @return array<string, mixed>
     */
    private static function datetime(string $name, string $label, array $rules): array
    {
        return self::field($name, $label, 'datetime-local', $rules);
    }

    /** @return array<string, mixed> */
    private static function checkbox(string $name, string $label, bool $list = false): array
    {
        return self::field($name, $label, 'checkbox', ['required', 'boolean'], $list);
    }

    /** @param class-string<Model> $model
     * @param  array<int, mixed>  $rules
     * @return array<string, mixed>
     */
    private static function select(
        string $name,
        string $label,
        string $model,
        string $optionLabel,
        array $rules,
        bool $list = false,
    ): array {
        return self::field($name, $label, 'select', $rules, $list, extra: [
            'source' => [$model, $optionLabel],
        ]);
    }

    /** @param array<string, string> $options
     * @param  array<int, mixed>  $rules
     * @return array<string, mixed>
     */
    private static function selectChoices(
        string $name,
        string $label,
        array $options,
        array $rules,
        bool $list = false,
    ): array {
        return self::field($name, $label, 'select', $rules, $list, extra: ['options' => $options]);
    }

    /** @param class-string<Model> $model
     * @return array<string, mixed>
     */
    private static function multiselect(
        string $name,
        string $label,
        string $model,
        string $optionLabel,
        string $relation,
    ): array {
        return self::field($name, $label, 'multiselect', ['nullable', 'array'], wide: true, extra: [
            'source' => [$model, $optionLabel],
            'relation' => $relation,
        ]);
    }

    /** @param array<int, mixed> $rules
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private static function field(
        string $name,
        string $label,
        string $type,
        array $rules,
        bool $list = false,
        bool $wide = false,
        ?string $unique = null,
        array $extra = [],
    ): array {
        return Arr::collapse([
            compact('name', 'label', 'type', 'rules', 'list', 'wide'),
            $unique ? ['unique' => $unique] : [],
            $extra,
        ]);
    }
}
