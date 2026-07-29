@props(['definition', 'resourceKey', 'rows'])

@php
    $columns = collect($definition['fields'])->where('list', true);
    $permissionResource = str($resourceKey)->replace('-', '_')->toString();
@endphp

<x-adminpanel::components.responsive-table>
    <table class="table min-w-[850px]">
        <thead>
            <tr>
                <th class="w-16">No.</th>
                @foreach ($columns as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
                <th class="w-40">{{ __('admin.common.updated_at') }}</th>
                <th class="sticky end-0 w-28 bg-base-100 text-end">{{ __('admin.common.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td class="text-base-content/60">{{ $rows->firstItem() + $loop->index }}</td>
                    @foreach ($columns as $column)
                        @php($value = data_get($row, $column['name']))
                        <td>
                            @if ($column['type'] === 'checkbox')
                                <span class="badge badge-sm badge-soft {{ $value ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $value ? __('admin.common.yes') : __('admin.common.no') }}
                                </span>
                            @elseif ($column['type'] === 'select')
                                {{ data_get($column['options'] ?? [], (string) $value, $value ?: '—') }}
                            @elseif ($value instanceof \DateTimeInterface)
                                {{ $value->translatedFormat('d M Y') }}
                            @else
                                <span class="line-clamp-2 max-w-md">{{ filled($value) ? $value : '—' }}</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="text-base-content/60 whitespace-nowrap text-sm">
                        {{ $row->updated_at?->diffForHumans() ?? '—' }}
                    </td>
                    <td class="sticky end-0 bg-base-100">
                        <div class="flex justify-end gap-1">
                            @can("update_{$permissionResource}")
                                @if (\App\Modules\CompanyProfile\Support\TranslatableContentRegistry::supports($resourceKey))
                                    <a href="{{ route('company-profile.translations.edit', ['resource' => $resourceKey, 'record' => $row->id]) }}"
                                        class="btn btn-square btn-text btn-sm" title="{{ __('company-profile.translations.title') }}">
                                        <span class="icon-[tabler--language] size-5"></span>
                                    </a>
                                @endif
                                <a href="{{ route('company-profile.content.edit', ['resource' => $resourceKey, 'record' => $row->id]) }}"
                                    class="btn btn-square btn-text btn-sm" title="{{ __('admin.crud.edit_action', ['resource' => $definition['singular']]) }}">
                                    <span class="icon-[tabler--edit] size-5"></span>
                                </a>
                            @endcan
                            @can("delete_{$permissionResource}")
                                <x-adminpanel::components.confirm-delete
                                    :action="route('company-profile.content.destroy', ['resource' => $resourceKey, 'record' => $row->id])"
                                    :name="data_get($row, 'title', data_get($row, 'name', $definition['singular']))"
                                    :title="__('admin.crud.delete_title', ['resource' => $definition['singular']])" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $columns->count() + 3 }}" class="py-14 text-center">
                        <span class="icon-[{{ $definition['icon'] }}] text-base-content/30 mx-auto size-10"></span>
                        <p class="mt-3 font-medium">{{ __('admin.crud.empty_title', ['resource' => $definition['title']]) }}</p>
                        <p class="text-base-content/50 mt-1 text-sm">{{ __('admin.crud.empty_description') }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</x-adminpanel::components.responsive-table>
