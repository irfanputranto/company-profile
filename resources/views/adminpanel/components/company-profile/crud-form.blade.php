@props(['definition', 'row' => null])

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    @foreach ($definition['fields'] as $field)
        @php
            $name = $field['name'];
            $rawValue = isset($field['relation']) && $row
                ? $row->{$field['relation']}->pluck('id')->all()
                : data_get($row, $name);
            $value = old($name, $rawValue);
            $isRequired = in_array('required', $field['rules'], true);

            if ($field['type'] === 'json' && ! is_string($value) && $value !== null) {
                $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            if ($value instanceof \DateTimeInterface) {
                $value = $field['type'] === 'datetime-local'
                    ? $value->format('Y-m-d\TH:i')
                    : $value->format('Y-m-d');
            }
        @endphp

        <div class="{{ $field['wide'] ? 'md:col-span-2' : '' }}">
            @if ($field['type'] === 'checkbox')
                <input type="hidden" name="{{ $name }}" value="0">
                <label class="border-base-content/20 flex cursor-pointer items-center justify-between gap-4 rounded-lg border p-4"
                    for="{{ $name }}">
                    <span>
                        <span class="block font-medium">{{ $field['label'] }}</span>
                        <span class="text-base-content/50 mt-0.5 block text-xs">{{ __('admin.crud.enabled_hint') }}</span>
                    </span>
                    <input id="{{ $name }}" name="{{ $name }}" type="checkbox" value="1"
                        class="switch switch-primary" @checked((bool) $value)>
                </label>
            @else
                <label class="label-text font-medium" for="{{ $name }}">
                    {{ $field['label'] }}
                    @if ($isRequired)<span class="text-error">*</span>@endif
                </label>

                @if ($field['type'] === 'multiselect')
                    <div class="mt-2">
                        <x-adminpanel::components.searchable-select
                            id="{{ $name }}"
                            name="{{ $name }}[]"
                            :multiple="true"
                            :invalid="$errors->has($name) || $errors->has($name.'.*')"
                            :placeholder="__('admin.crud.multiselect_placeholder', ['resource' => $field['label']])"
                            :search-placeholder="__('admin.crud.multiselect_search', ['resource' => $field['label']])"
                            :no-results-text="__('admin.select.no_results')"
                            :required="$isRequired">
                            @foreach ($field['options'] ?? [] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}"
                                    @selected(in_array((string) $optionValue, array_map('strval', (array) $value), true))>
                                    {{ $optionLabel }}
                                </option>
                            @endforeach
                        </x-adminpanel::components.searchable-select>
                    </div>
                    <p class="text-base-content/50 mt-1.5 flex items-center gap-1.5 text-xs">
                        <span class="icon-[tabler--search] size-3.5 shrink-0"></span>
                        {{ __('admin.crud.multiselect_hint') }}
                    </p>
                @elseif ($field['type'] === 'select')
                    <select id="{{ $name }}" name="{{ $name }}"
                        class="select mt-2 w-full @error($name) select-error @enderror"
                        @required($isRequired)>
                        <option value="">{{ __('admin.crud.select', ['resource' => $field['label']]) }}</option>
                        @foreach ($field['options'] ?? [] as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}"
                                @selected((string) $value === (string) $optionValue)>
                                {{ $optionLabel }}
                            </option>
                        @endforeach
                    </select>
                @elseif (in_array($field['type'], ['textarea', 'json'], true))
                    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $field['type'] === 'json' ? 7 : 5 }}"
                        class="textarea mt-2 w-full font-{{ $field['type'] === 'json' ? 'mono' : 'sans' }} @error($name) textarea-error @enderror"
                        @required($isRequired)>{{ $value }}</textarea>
                @else
                    <input id="{{ $name }}" name="{{ $name }}" type="{{ $field['type'] }}"
                        value="{{ $value }}" step="{{ $field['step'] ?? null }}"
                        class="input mt-2 w-full @error($name) input-error @enderror"
                        @required($isRequired)>
                @endif
            @endif

            @error($name)<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
            @error($name.'.*')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
        </div>
    @endforeach
</div>
