<?php

namespace App\Modules\ProjectManagement\Requests;

use App\Models\ProjectServer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectServerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update_managed_projects') ?? false;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        $server = $this->route('server');
        $secretRules = $server instanceof ProjectServer ? ['nullable', 'string', 'max:5000'] : ['required', 'string', 'max:5000'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'max:255'],
            'environment' => ['required', Rule::in(['production', 'staging', 'development', 'other'])],
            'host' => ['nullable', 'string', 'max:2048'],
            'port' => ['nullable', 'integer', 'between:1,65535'],
            'username' => $secretRules,
            'password' => $secretRules,
            'api_secret' => ['nullable', 'string', 'max:10000'],
            'credentials_note' => ['nullable', 'string', 'max:10000'],
            'billing_cycle' => ['required', Rule::in(['monthly', 'quarterly', 'yearly', 'one_time'])],
            'base_price' => ['required', 'numeric', 'min:0', 'max:9999999999999999.99'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:9999999999999999.99'],
            'currency' => ['required', Rule::in(['IDR', 'USD', 'SGD'])],
            'purchased_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:purchased_at'],
            'reminder_days' => ['required', 'integer', 'between:1,365'],
            'status' => ['required', Rule::in(['active', 'expired', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
