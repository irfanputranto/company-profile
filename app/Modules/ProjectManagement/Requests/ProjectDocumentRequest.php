<?php

namespace App\Modules\ProjectManagement\Requests;

use App\Support\SecureUploadRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update_managed_projects') ?? false;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::in(['contract', 'syllabus', 'requirement', 'design', 'report', 'invoice', 'other'])],
            'notes' => ['nullable', 'string', 'max:2000'],
            'documents' => ['required', 'array', 'min:1', 'max:10'],
            'documents.*' => SecureUploadRules::document(),
        ];
    }
}
