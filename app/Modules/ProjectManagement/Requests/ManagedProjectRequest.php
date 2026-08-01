<?php

namespace App\Modules\ProjectManagement\Requests;

use App\Models\ManagedProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManagedProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->isMethod('POST') ? 'create_managed_projects' : 'update_managed_projects';

        return $this->user()?->can($permission) ?? false;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        $project = $this->route('managed_project');
        $projectId = $project instanceof ManagedProject ? $project->getKey() : null;

        return [
            'client_company_id' => ['required', 'integer', Rule::exists('client_companies', 'id')->whereNull('deleted_at')],
            'code' => ['required', 'string', 'max:80', Rule::unique('managed_projects', 'code')->ignore($projectId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'status' => ['required', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'started_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'contract_value' => ['required', 'numeric', 'min:0', 'max:9999999999999999.99'],
            'estimated_cost' => ['required', 'numeric', 'min:0', 'max:9999999999999999.99'],
            'currency' => ['required', Rule::in(['IDR', 'USD', 'SGD'])],
        ];
    }
}
