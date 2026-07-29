<?php

namespace App\Modules\CompanyProfile\Services;

use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LanguageManager
{
    public function __construct(private LanguageResolver $resolver) {}

    public function save(Language $language, array $attributes): Language
    {
        return DB::transaction(function () use ($language, $attributes): Language {
            $makeDefault = (bool) ($attributes['is_default'] ?? false);
            $attributes['is_active'] = $makeDefault ? true : (bool) ($attributes['is_active'] ?? false);

            $language->fill($attributes);
            $language->save();

            if ($makeDefault || ! Language::query()->where('is_default', true)->exists()) {
                $this->setDefault($language);
            }

            $this->resolver->forget();

            return $language->refresh();
        });
    }

    public function setDefault(Language $language): void
    {
        DB::transaction(function () use ($language): void {
            Language::query()->lockForUpdate()->get();

            Language::query()->whereKeyNot($language->getKey())->where('is_default', true)
                ->get()
                ->each(function (Language $otherLanguage): void {
                    $otherLanguage->update(['is_default' => false]);
                });

            $language->update(['is_default' => true, 'is_active' => true]);

            SiteSetting::query()->updateOrCreate(
                ['key' => 'site.default_language'],
                ['group' => 'localization', 'type' => 'string', 'value' => $language->code, 'is_public' => true],
            );
        });

        $this->resolver->forget();
    }

    public function delete(Language $language): void
    {
        if ($language->is_default) {
            throw ValidationException::withMessages([
                'language' => __('company-profile.languages.cannot_delete_default'),
            ]);
        }

        if ($language->contentTranslations()->exists()) {
            throw ValidationException::withMessages([
                'language' => __('company-profile.languages.cannot_delete_used'),
            ]);
        }

        $language->delete();
        $this->resolver->forget();
    }
}
