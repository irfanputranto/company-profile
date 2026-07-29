<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'author_id', 'article_category_id', 'slug', 'title', 'excerpt', 'content', 'status',
        'is_featured', 'reading_time_minutes', 'published_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'reading_time_minutes' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsToMany<Tag, $this> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
