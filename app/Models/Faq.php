<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class Faq extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = ['profile_id', 'question', 'answer', 'sort_order', 'is_active'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_active' => 'boolean'];
    }
}
