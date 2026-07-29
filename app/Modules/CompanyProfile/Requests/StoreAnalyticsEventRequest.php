<?php

namespace App\Modules\CompanyProfile\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnalyticsEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $scopeType = (string) $this->input('scope_type');
        $allowedEvents = config("analytics.events.{$scopeType}", []);

        return [
            'scope_type' => ['required', Rule::in(array_keys(config('analytics.events', [])))],
            'event' => ['required', 'string', Rule::in(array_keys(is_array($allowedEvents) ? $allowedEvents : []))],
        ];
    }
}
