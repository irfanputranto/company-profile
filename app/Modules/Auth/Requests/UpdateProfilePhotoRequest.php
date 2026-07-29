<?php

namespace App\Modules\Auth\Requests;

use App\Support\SecureUploadRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'photo' => SecureUploadRules::image(),
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }
}
