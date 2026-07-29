<x-adminpanel::layouts.app :title="__('company-profile.languages.title')">
    <x-adminpanel::components.page-header :title="__('company-profile.languages.title')" :description="__('company-profile.languages.description')">
        <x-slot:actions>
            @can('create_languages')
                <a href="{{ route('company-profile.languages.create') }}" class="btn btn-primary">
                    <span class="icon-[tabler--plus] size-5"></span>{{ __('company-profile.languages.create') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-adminpanel::components.page-header>

    <x-adminpanel::components.flash-message />
    @error('language')<div class="alert alert-error mt-6">{{ $message }}</div>@enderror

    <section class="card mt-6 overflow-hidden shadow-md">
        <x-adminpanel::components.responsive-table>
            <table class="table min-w-[760px]">
                <thead><tr>
                    <th>{{ __('company-profile.languages.code') }}</th>
                    <th>{{ __('company-profile.languages.name') }}</th>
                    <th>{{ __('company-profile.languages.native_name') }}</th>
                    <th>{{ __('company-profile.languages.direction') }}</th>
                    <th>{{ __('company-profile.languages.status') }}</th>
                    <th class="text-end">{{ __('company-profile.actions.actions') }}</th>
                </tr></thead>
                <tbody>
                    @forelse ($languages as $language)
                        <tr>
                            <td><span class="badge badge-soft badge-primary">{{ $language->code }}</span></td>
                            <td>{{ $language->name }}</td>
                            <td>{{ $language->native_name }}</td>
                            <td>{{ strtoupper($language->direction) }}</td>
                            <td class="space-x-1">
                                @if ($language->is_default)<span class="badge badge-success badge-soft">{{ __('company-profile.languages.default') }}</span>@endif
                                <span class="badge badge-soft {{ $language->is_active ? 'badge-info' : 'badge-neutral' }}">
                                    {{ $language->is_active ? __('company-profile.languages.active') : __('company-profile.languages.inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    @can('update_languages')
                                        <a href="{{ route('company-profile.languages.edit', $language) }}" class="btn btn-square btn-text btn-sm">
                                            <span class="icon-[tabler--edit] size-5"></span>
                                        </a>
                                    @endcan
                                    @can('delete_languages')
                                        <x-adminpanel::components.confirm-delete :action="route('company-profile.languages.destroy', $language)"
                                            :name="$language->native_name" :title="__('company-profile.languages.delete')" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center">{{ __('company-profile.languages.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-adminpanel::components.responsive-table>
        <div class="border-base-content/10 border-t p-4">
            <x-adminpanel::components.pagination :paginator="$languages" />
        </div>
    </section>
</x-adminpanel::layouts.app>
