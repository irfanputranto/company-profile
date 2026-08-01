<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ContentPage extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'author_id', 'slug', 'title', 'template', 'content', 'status', 'show_in_navigation',
        'sort_order', 'published_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'show_in_navigation' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /** @param Builder<ContentPage> $query */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(fn (Builder $publicationQuery): Builder => $publicationQuery
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now()));
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && ($this->published_at === null || $this->published_at->isPast());
    }

    /** @return MorphOne<SeoMetadata, $this> */
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }
}
