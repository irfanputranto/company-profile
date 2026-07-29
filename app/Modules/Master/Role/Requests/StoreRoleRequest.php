<?php

namespace App\Modules\Master\Role\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->isMethod('POST') ? 'create_roles' : 'update_roles';

        return $this->user()?->can($permission) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $permissionIds = collect((array) $this->input('permission_ids', []))
            ->filter(fn (mixed $permissionId): bool => filled($permissionId))
            ->map(fn (mixed $permissionId): int => (int) $permissionId)
            ->values()
            ->all();

        $this->merge([
            'name' => str((string) $this->input('name', ''))->squish()->lower()->toString(),
            'guard_name' => 'web',
            'permission_ids' => $permissionIds,
        ]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return $this->roleRules();
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    protected function roleRules(?int $roleId = null): array
    {
        $uniqueName = Rule::unique('roles', 'name')
            ->where(fn (Builder $query): Builder => $query->where('guard_name', 'web'))
            ->ignore($roleId);

        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:[ _.-][a-z0-9]+)*$/', $uniqueName],
            'guard_name' => ['required', Rule::in(['web'])],
            'permission_ids' => ['required', 'array', 'min:1'],
            'permission_ids.*' => ['distinct', 'integer', Rule::exists('permissions', 'id')->where('guard_name', 'web')],
        ];
    }
}
