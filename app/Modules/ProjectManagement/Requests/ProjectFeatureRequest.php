<?php

namespace App\Modules\ProjectManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update_managed_projects') ?? false;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'acceptance_criteria' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['backlog', 'in_progress', 'review', 'done', 'blocked'])],
            'sort_order' => ['required', 'integer', 'between:0,65535'],
        ];
    }
}
