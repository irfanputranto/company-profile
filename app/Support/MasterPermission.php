<?php

namespace App\Support;

class MasterPermission
{
    public const ACTIONS = ['view', 'create', 'show', 'delete', 'update'];

    public const RESOURCES = [
        'users',
        'roles',
        'permissions',
        'profiles',
        'social_links',
        'experiences',
        'educations',
        'skill_categories',
        'skills',
        'services',
        'features',
        'pricing_plans',
        'projects',
        'testimonials',
        'faqs',
        'content_pages',
        'article_categories',
        'articles',
        'tags',
        'site_settings',
        'seo_metadata',
        'media',
        'languages',
        'client_companies',
        'managed_projects',
    ];

    public const LEGACY_PERMISSIONS = [
        'manage users',
        'manage permissions',
    ];

    public const SPECIAL_PERMISSIONS = [
        'show_project_credentials',
    ];

    /** @return list<string> */
    public static function all(): array
    {
        return collect(self::RESOURCES)
            ->flatMap(fn (string $resource): array => array_map(
                fn (string $action): string => self::name($action, $resource),
                self::ACTIONS,
            ))
            ->concat(self::SPECIAL_PERMISSIONS)
            ->values()
            ->all();
    }

    public static function name(string $action, string $resource): string
    {
        return $action.'_'.$resource;
    }

    /** @param list<string> $resources
     * @return list<string>
     */
    public static function forResources(array $resources): array
    {
        return collect($resources)
            ->flatMap(fn (string $resource): array => array_map(
                fn (string $action): string => self::name($action, $resource),
                self::ACTIONS,
            ))
            ->values()
            ->all();
    }
}
