<?php

namespace App\Models;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends AuditableModel
{
    /** @use HasFactory<LanguageFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'code', 'name', 'native_name', 'direction', 'is_default', 'is_active', 'sort_order',
    ];

    /** @return HasMany<ContentTranslation, $this> */
    public function contentTranslations(): HasMany
    {
        return $this->hasMany(ContentTranslation::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
