<?php

namespace App\Modules\Master\Permission\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->isMethod('POST') ? 'create_permissions' : 'update_permissions';

        return $this->user()?->can($permission) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => str((string) $this->input('name', ''))->squish()->lower()->toString(),
            'guard_name' => 'web',
        ]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return $this->permissionRules();
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    protected function permissionRules(?int $permissionId = null): array
    {
        $uniqueName = Rule::unique('permissions', 'name')
            ->where(fn (Builder $query): Builder => $query->where('guard_name', 'web'))
            ->ignore($permissionId);

        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:[ _.-][a-z0-9]+)*$/', $uniqueName],
            'guard_name' => ['required', Rule::in(['web'])],
        ];
    }
}
