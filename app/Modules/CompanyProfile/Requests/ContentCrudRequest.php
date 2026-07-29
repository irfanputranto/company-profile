<?php

namespace App\Modules\CompanyProfile\Requests;

use App\Modules\CompanyProfile\Support\ContentResourceRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ContentCrudRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $definition = ContentResourceRegistry::get((string) $this->route('resource'));
        $slugSource = $definition['slugSource'];

        if ($slugSource && blank($this->input('slug')) && filled($this->input($slugSource))) {
            $this->merge([
                'slug' => Str::slug((string) $this->input($slugSource)),
            ]);
        }
    }

    public function authorize(): bool
    {
        $action = $this->isMethod('POST') ? 'create' : 'update';

        return $this->user()?->can("{$action}_{$this->permissionResource()}") ?? false;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ContentResourceRegistry::rules(
            resource: (string) $this->route('resource'),
            recordId: $this->route('record') ? (int) $this->route('record') : null,
        );
    }

    private function permissionResource(): string
    {
        return str((string) $this->route('resource'))->replace('-', '_')->toString();
    }
}
