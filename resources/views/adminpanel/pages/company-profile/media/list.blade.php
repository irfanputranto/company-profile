<x-adminpanel::layouts.app :title="__('admin.media.title')">
    <x-adminpanel::components.page-header :title="__('admin.media.title')" :description="__('admin.media.description')">
        <x-slot:actions>
            @can('create_media')
                <a href="{{ route('company-profile.media.create') }}" class="btn btn-primary">
                    <span class="icon-[tabler--upload] size-5"></span>{{ __('admin.media.upload') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-adminpanel::components.page-header>

    <x-adminpanel::components.flash-message />

    <section class="card shadow-base-300/10 mt-6 overflow-hidden shadow-md">
        <div class="card-body border-base-content/10 border-b p-4 sm:p-5">
            <x-adminpanel::components.search-toolbar
                :action="route('company-profile.media.index')"
                :value="request('q')"
                :per-page="$list->perPage()"
                :placeholder="__('admin.media.search')" />
        </div>

        <x-adminpanel::components.responsive-table>
            <table class="table min-w-[900px]">
                <thead>
                    <tr>
                        <th class="w-20">{{ __('admin.media.preview') }}</th>
                        <th>{{ __('admin.media.title') }}</th>
                        <th>{{ __('admin.media.alt_text') }}</th>
                        <th class="w-28">{{ __('admin.media.size') }}</th>
                        <th class="w-24">{{ __('admin.media.variants') }}</th>
                        <th class="sticky end-0 w-28 bg-base-100 text-end">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($list as $media)
                        <tr>
                            <td>
                                <img src="{{ $media->publicUrl() }}" alt="{{ $media->alt_text }}"
                                    class="size-14 rounded-lg object-cover" loading="lazy">
                            </td>
                            <td>
                                <p class="max-w-xs truncate font-medium">{{ $media->original_name }}</p>
                                <p class="text-base-content/50 mt-1 text-xs">{{ $media->width }}×{{ $media->height }} · WebP</p>
                            </td>
                            <td><p class="line-clamp-2 max-w-sm">{{ $media->alt_text }}</p></td>
                            <td>{{ \Illuminate\Support\Number::fileSize($media->byte_size) }}</td>
                            <td><span class="badge badge-primary badge-soft">{{ $media->variants->count() }}</span></td>
                            <td class="sticky end-0 bg-base-100">
                                <div class="flex justify-end gap-1">
                                    @can('update_media')
                                        <a href="{{ route('company-profile.media.edit', $media->uuid) }}"
                                            class="btn btn-square btn-text btn-sm" title="{{ __('admin.media.edit_title') }}">
                                            <span class="icon-[tabler--edit] size-5"></span>
                                        </a>
                                    @endcan
                                    @can('delete_media')
                                        <x-adminpanel::components.confirm-delete
                                            :action="route('company-profile.media.destroy', $media->uuid)"
                                            :name="$media->original_name" :title="__('admin.media.delete')" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-14 text-center"><span class="icon-[tabler--photo-off] text-base-content/30 mx-auto size-10"></span><p class="mt-3 font-medium">{{ __('admin.media.empty') }}</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-adminpanel::components.responsive-table>

        <div class="border-base-content/10 border-t px-4 py-4 sm:px-5">
            <x-adminpanel::components.pagination :paginator="$list" />
        </div>
    </section>
</x-adminpanel::layouts.app>
