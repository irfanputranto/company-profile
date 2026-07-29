@props([
    'action',
    'filters' => [],
    'preserve' => [],
])

@php
    $activeFilterCount = collect($filters)->filter(fn ($filter) => filled($filter['value'] ?? null))->count();
    $resetQuery = collect($preserve)->filter(fn ($value) => filled($value))->all();
    $resetUrl = $action . ($resetQuery === [] ? '' : '?' . http_build_query($resetQuery));
@endphp

@if ($filters !== [])
    <form method="GET" action="{{ $action }}" x-data="dataTableFilters" x-ref="filterForm"
        {{ $attributes->merge(['class' => 'w-full']) }}>
        @foreach ($preserve as $preserveName => $preserveValue)
            @if (filled($preserveValue))
                <input type="hidden" name="{{ $preserveName }}" value="{{ $preserveValue }}">
            @endif
        @endforeach

        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="icon-[tabler--filter] text-primary size-5"></span>
                    <h2 class="text-base-content text-sm font-semibold">Filter Data</h2>
                    @if ($activeFilterCount > 0)
                        <span class="badge badge-primary badge-sm badge-soft">{{ $activeFilterCount }} aktif</span>
                    @endif
                </div>

                @if ($activeFilterCount > 0)
                    <a href="{{ $resetUrl }}" class="btn btn-text btn-sm text-error">
                        <span class="icon-[tabler--filter-off] size-4.5"></span>
                        Reset filter
                    </a>
                @endif
            </div>

            <div class="grid items-end gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($filters as $filter)
                    <div class="min-w-0">
                        @switch($filter['type'])
                            @case('select')
                                <label class="label-text mb-1.5 block text-xs font-medium" for="filter_{{ $loop->index }}_{{ $filter['name'] }}">
                                    {{ $filter['label'] }}
                                </label>
                                <x-adminpanel::components.searchable-select name="{{ $filter['name'] }}"
                                    id="filter_{{ $loop->index }}_{{ $filter['name'] }}"
                                    :placeholder="$filter['placeholder'] ?? 'Semua'"
                                    search-placeholder="Cari pilihan..." @change="applyFilters">
                                    <option value="">{{ $filter['placeholder'] ?? 'Semua' }}</option>
                                    @foreach ($filter['options'] ?? [] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) ($filter['value'] ?? '') === (string) $optionValue)>
                                            {{ $optionLabel }}
                                        </option>
                                    @endforeach
                                </x-adminpanel::components.searchable-select>
                            @break

                            @case('date')
                                <div class="input-floating w-full" x-data="dateInputPicker()">
                                    <input x-ref="input" id="filter_{{ $loop->index }}_{{ preg_replace('/[^a-zA-Z0-9_\\-]/', '_', $filter['name']) }}" type="text" name="{{ $filter['name'] }}"
                                        value="{{ $filter['value'] ?? '' }}" placeholder="YYYY-MM-DD" class="input w-full" @change="applyFilters">
                                    <label class="input-floating-label" for="filter_{{ $loop->index }}_{{ preg_replace('/[^a-zA-Z0-9_\\-]/', '_', $filter['name']) }}">
                                        {{ $filter['label'] }}
                                    </label>
                                </div>
                            @break

                            @default
                                <label class="label-text mb-1.5 block text-xs font-medium" for="filter_{{ $loop->index }}_{{ $filter['name'] }}">
                                    {{ $filter['label'] }}
                                </label>
                                <input type="text" name="{{ $filter['name'] }}" value="{{ $filter['value'] ?? '' }}"
                                    id="filter_{{ $loop->index }}_{{ $filter['name'] }}"
                                    class="input w-full" placeholder="{{ $filter['placeholder'] ?? 'Masukkan nilai' }}"
                                    @input.debounce.500ms="applyFilters">
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>
    </form>
@endif
