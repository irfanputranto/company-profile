<?php

namespace App\Models\Concerns;

use App\Models\ContentTranslation;
use App\Modules\CompanyProfile\Services\LanguageResolver;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContentTranslations
{
    /** @return MorphMany<ContentTranslation, $this> */
    public function contentTranslations(): MorphMany
    {
        return $this->morphMany(ContentTranslation::class, 'translatable');
    }

    public function translated(string $field, ?string $locale = null): mixed
    {
        $resolver = app(LanguageResolver::class);
        $locale ??= app()->getLocale();

        $translation = $this->translationValue($field, $locale);

        if ($translation !== null) {
            return $translation;
        }

        $fallback = $resolver->defaultCode();

        if ($fallback !== $locale && ($translation = $this->translationValue($field, $fallback)) !== null) {
            return $translation;
        }

        return $this->getAttribute($field);
    }

    private function translationValue(string $field, string $locale): ?string
    {
        if ($this->relationLoaded('contentTranslations')) {
            return $this->contentTranslations
                ->first(fn (ContentTranslation $translation): bool => $translation->field === $field
                    && $translation->language?->code === $locale)
                ?->value;
        }

        return $this->contentTranslations()
            ->where('field', $field)
            ->whereHas('language', fn ($query) => $query->where('code', $locale))
            ->value('value');
    }
}
