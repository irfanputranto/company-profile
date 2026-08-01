<?php

namespace App\Modules\ProjectManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectTechnologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update_managed_projects') ?? false;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:50'],
            'version' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
