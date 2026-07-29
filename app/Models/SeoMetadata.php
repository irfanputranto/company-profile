<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class SeoMetadata extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'seoable_type', 'seoable_id', 'meta_title', 'meta_description', 'canonical_url',
        'robots_index', 'robots_follow', 'open_graph_title', 'open_graph_description',
        'open_graph_media_id', 'twitter_card', 'structured_data',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'structured_data' => 'array',
        ];
    }
}
