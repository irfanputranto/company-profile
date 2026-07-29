<?php

namespace App\Modules\CompanyProfile\Support;

class TranslatableContentRegistry
{
    /** @var array<string, list<string>> */
    private const FIELDS = [
        'profiles' => ['headline', 'short_bio', 'about'],
        'social-links' => ['label'],
        'experiences' => ['role', 'summary'],
        'educations' => ['degree', 'field_of_study', 'description'],
        'skill-categories' => ['name'],
        'skills' => ['name'],
        'services' => ['title', 'summary', 'content', 'call_to_action_label'],
        'features' => ['title', 'description'],
        'pricing-plans' => ['title', 'tagline', 'description', 'call_to_action_label'],
        'projects' => ['title', 'summary', 'content'],
        'testimonials' => ['client_role', 'quote'],
        'faqs' => ['question', 'answer'],
        'content-pages' => ['title', 'content'],
        'article-categories' => ['name', 'description'],
        'articles' => ['title', 'excerpt', 'content'],
        'tags' => ['name'],
        'seo-metadata' => [
            'meta_title', 'meta_description', 'open_graph_title', 'open_graph_description',
        ],
    ];

    /** @return list<string> */
    public static function fields(string $resource): array
    {
        return self::FIELDS[$resource] ?? abort(404);
    }

    public static function supports(string $resource): bool
    {
        return array_key_exists($resource, self::FIELDS);
    }
}
