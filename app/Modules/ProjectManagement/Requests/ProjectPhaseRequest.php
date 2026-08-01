<?php

namespace App\Modules\ProjectManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectPhaseRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:10000'],
            'deliverables' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'review', 'completed', 'blocked'])],
            'progress' => ['required', 'integer', 'between:0,100'],
            'started_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'sort_order' => ['required', 'integer', 'between:0,65535'],
        ];
    }
}
