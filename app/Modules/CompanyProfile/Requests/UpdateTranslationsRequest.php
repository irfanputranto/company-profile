<?php

namespace App\Modules\CompanyProfile\Requests;

use App\Models\Language;
use App\Modules\CompanyProfile\Support\TranslatableContentRegistry;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $resource = str((string) $this->route('resource'))->replace('-', '_')->toString();

        return $this->user()?->can("update_{$resource}") ?? false;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $languageCodes = Language::query()->where('is_active', true)->pluck('code');
        $rules = ['translations' => ['required', 'array:'.$languageCodes->implode(',')]];
        $fields = TranslatableContentRegistry::fields((string) $this->route('resource'));

        foreach ($languageCodes as $code) {
            $rules["translations.{$code}"] = ['required', 'array:'.implode(',', $fields)];

            foreach ($fields as $field) {
                $rules["translations.{$code}.{$field}"] = ['nullable', 'string', 'max:1000000'];
            }
        }

        return $rules;
    }
}
