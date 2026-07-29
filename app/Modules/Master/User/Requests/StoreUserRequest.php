<?php

namespace App\Modules\Master\User\Requests;

use App\Support\SecureUploadRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->isMethod('POST') ? 'create_users' : 'update_users';

        return $this->user()?->can($permission) ?? false;
    }

    public function rules(): array
    {
        return $this->userRules();
    }

    protected function userRules(?int $userId = null, bool $passwordRequired = true): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'photo' => SecureUploadRules::image(),
            'remove_photo' => ['nullable', 'boolean'],
            'password' => [$passwordRequired ? 'required' : 'nullable', 'confirmed', Password::min(12)->mixedCase()->letters()->numbers()->symbols()],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
