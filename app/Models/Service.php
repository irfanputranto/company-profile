<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class Service extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'profile_id', 'slug', 'title', 'summary', 'content', 'icon', 'starting_price', 'currency',
        'call_to_action_label', 'call_to_action_url', 'sort_order', 'is_featured', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
