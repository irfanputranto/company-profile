<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class SkillCategory extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = ['name', 'slug', 'sort_order'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }
}
