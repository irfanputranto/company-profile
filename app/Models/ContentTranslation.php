<?php

namespace App\Models;

use Database\Factories\ContentTranslationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentTranslation extends AuditableModel
{
    /** @use HasFactory<ContentTranslationFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'language_id', 'translatable_type', 'translatable_id', 'field', 'value',
    ];

    /** @return BelongsTo<Language, $this> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /** @return MorphTo<Model, $this> */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
