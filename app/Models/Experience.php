<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class Experience extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'profile_id', 'company', 'role', 'location', 'employment_type', 'started_at', 'ended_at',
        'is_current', 'summary', 'highlights', 'technologies', 'sort_order', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'is_current' => 'boolean',
            'highlights' => 'array',
            'technologies' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
