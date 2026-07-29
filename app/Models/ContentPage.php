<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class ContentPage extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'author_id', 'slug', 'title', 'template', 'content', 'status', 'show_in_navigation',
        'sort_order', 'published_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'show_in_navigation' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }
}
