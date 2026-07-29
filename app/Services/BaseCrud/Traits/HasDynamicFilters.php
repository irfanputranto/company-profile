<?php

namespace App\Services\BaseCrud\Traits;

trait HasDynamicFilters
{
    public function __extraDataList($data): array
    {
        $data = $this->__extraData($data);

        $data['filters'] = collect($this->filterFields)
            ->map(fn (array $filter): array => [
                ...$filter,
                'value' => $this->requestData->query($filter['name']),
            ])
            ->all();

        $data['activeFilters'] = collect($data['filters'])
            ->pluck('value', 'name')
            ->filter(fn ($value): bool => filled($value))
            ->all();

        return $data;
    }
}
