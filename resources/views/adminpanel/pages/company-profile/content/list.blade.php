@php($permissionResource = str($resourceKey)->replace('-', '_')->toString())

<x-adminpanel::layouts.app :title="$resourceDefinition['title']">
    <x-adminpanel::components.page-header
        :title="$resourceDefinition['title']"
        :description="__('admin.crud.manage_description', ['resource' => $resourceDefinition['title']])">
        <x-slot:actions>
            @can("create_{$permissionResource}")
                <a href="{{ route('company-profile.content.create', ['resource' => $resourceKey]) }}" class="btn btn-primary">
                    <span class="icon-[tabler--plus] size-5"></span>
                    {{ __('admin.crud.add', ['resource' => $resourceDefinition['singular']]) }}
                </a>
            @endcan
        </x-slot:actions>
    </x-adminpanel::components.page-header>

    <x-adminpanel::components.flash-message />

    <section class="card shadow-base-300/10 mt-6 overflow-hidden shadow-md">
        <div class="card-body border-base-content/10 border-b p-4 sm:p-5">
            <x-adminpanel::components.search-toolbar
                :action="route('company-profile.content.index', ['resource' => $resourceKey])"
                :value="request('q')"
                :per-page="$list->perPage()"
                :placeholder="__('admin.crud.search', ['resource' => $resourceDefinition['title']])" />
        </div>
        <x-adminpanel::components.company-profile.crud-table
            :definition="$resourceDefinition"
            :resource-key="$resourceKey"
            :rows="$list" />
        <div class="border-base-content/10 border-t px-4 py-4 sm:px-5">
            <x-adminpanel::components.pagination :paginator="$list" />
        </div>
    </section>
</x-adminpanel::layouts.app>
