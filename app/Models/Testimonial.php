<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class Testimonial extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'profile_id', 'client_name', 'client_role', 'company', 'quote', 'rating', 'sort_order',
        'is_featured', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
