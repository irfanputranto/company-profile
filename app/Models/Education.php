<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class Education extends AuditableModel
{
    use HasContentTranslations;

    protected $table = 'educations';

    /** @var list<string> */
    protected $fillable = [
        'profile_id', 'institution', 'degree', 'field_of_study', 'location', 'started_at', 'ended_at',
        'grade', 'grade_scale', 'description', 'sort_order', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'grade' => 'decimal:2',
            'grade_scale' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
