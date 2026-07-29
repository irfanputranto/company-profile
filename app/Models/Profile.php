<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class Profile extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'user_id', 'slug', 'public_name', 'headline', 'short_bio', 'about', 'email', 'phone',
        'location', 'timezone', 'availability_status', 'years_experience', 'resume_path', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
