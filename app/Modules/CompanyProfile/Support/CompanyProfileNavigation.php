<?php

namespace App\Modules\CompanyProfile\Support;

class CompanyProfileNavigation
{
    /** @return list<array{label: string, items: list<array<string, string>>}> */
    public static function groups(): array
    {
        return [
            [
                'label' => __('admin.navigation.profile'),
                'items' => [
                    self::content('profiles'),
                    self::content('social-links'),
                    self::content('experiences'),
                    self::content('educations'),
                ],
            ],
            [
                'label' => __('admin.navigation.portfolio'),
                'items' => [
                    self::content('skill-categories'),
                    self::content('skills'),
                    self::content('services'),
                    self::content('features'),
                    self::content('pricing-plans'),
                    self::content('projects'),
                    self::content('testimonials'),
                    self::content('faqs'),
                ],
            ],
            [
                'label' => __('admin.navigation.content'),
                'items' => [
                    self::content('content-pages'),
                    self::content('article-categories'),
                    self::content('articles'),
                    self::content('tags'),
                ],
            ],
            [
                'label' => __('admin.navigation.website'),
                'items' => [
                    [
                        'label' => __('company-profile.languages.title'),
                        'permission' => 'view_languages',
                        'route' => 'company-profile.languages.index',
                    ],
                    self::content('site-settings'),
                    self::content('seo-metadata'),
                    [
                        'label' => __('admin.media.title'),
                        'permission' => 'view_media',
                        'route' => 'company-profile.media.index',
                    ],
                    [
                        'label' => __('admin.analytics.title'),
                        'permission' => 'view_analytics',
                        'route' => 'company-profile.analytics.index',
                    ],
                ],
            ],
        ];
    }

    /** @return array<string, string> */
    private static function content(string $resource): array
    {
        return [
            'label' => __("admin.resources.{$resource}.title"),
            'permission' => 'view_'.str($resource)->replace('-', '_')->toString(),
            'route' => 'company-profile.content.index',
            'resource' => $resource,
        ];
    }
}
