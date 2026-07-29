<?php

namespace App\Modules\CompanyProfile\Services;

use App\Models\ContentTranslation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TranslationManager
{
    /** @param array<string, array<string, string|null>> $translations */
    public function save(Model $model, array $translations): void
    {
        DB::transaction(function () use ($model, $translations): void {
            $languages = Language::query()
                ->whereIn('code', array_keys($translations))
                ->where('is_active', true)
                ->pluck('id', 'code');

            foreach ($translations as $code => $fields) {
                $languageId = $languages->get($code);

                if (! $languageId) {
                    continue;
                }

                foreach ($fields as $field => $value) {
                    if (blank($value)) {
                        $model->contentTranslations()
                            ->where('language_id', $languageId)
                            ->where('field', $field)
                            ->first()
                            ?->delete();

                        continue;
                    }

                    $translation = ContentTranslation::withTrashed()->firstOrNew([
                        'language_id' => $languageId,
                        'translatable_type' => $model->getMorphClass(),
                        'translatable_id' => $model->getKey(),
                        'field' => $field,
                    ]);
                    $translation->fill(['value' => $value]);

                    if ($translation->trashed()) {
                        $translation->restore();
                    }

                    $translation->save();
                }
            }

            activity('model')
                ->performedOn($model)
                ->event('translations_updated')
                ->withProperties(['locales' => array_keys($translations)])
                ->log('Mengubah terjemahan '.class_basename($model));
        });
    }
}
