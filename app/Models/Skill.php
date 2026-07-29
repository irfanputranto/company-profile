<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'profile_id', 'skill_category_id', 'name', 'slug', 'proficiency', 'years_experience',
        'sort_order', 'is_featured', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'proficiency' => 'integer',
            'years_experience' => 'integer',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<SkillCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }
}
