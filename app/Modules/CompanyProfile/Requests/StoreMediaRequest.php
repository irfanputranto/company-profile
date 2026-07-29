<?php

namespace App\Modules\CompanyProfile\Requests;

use App\Support\SecureUploadRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create_media') ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'image' => SecureUploadRules::image('required'),
            'alt_text' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
