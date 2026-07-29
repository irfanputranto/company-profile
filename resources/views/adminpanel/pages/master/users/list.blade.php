<x-adminpanel::layouts.app :title="__('admin.users.master_title')">
    <x-adminpanel::components.page-header :title="__('admin.users.title')" :actions-first="true" :description="__('admin.users.description')"><x-slot:actions>@can('create_users')<a href="{{ route('master.users.create') }}" class="btn btn-primary"><span class="icon-[tabler--plus] size-5"></span>{{ __('admin.users.add') }}</a>@endcan</x-slot:actions></x-adminpanel::components.page-header>
    <x-adminpanel::components.flash-message />
    <section class="card shadow-base-300/10 overflow-hidden shadow-md">
        <div class="bg-base-200/40 border-base-content/10 border-b p-4 sm:p-5"><x-adminpanel::components.dynamic-filters :action="route('master.users.index')" :filters="$filters" :preserve="['q'=>request('q'),'limit'=>$list->perPage()]" /></div>
        <div class="card-body border-base-content/10 border-b p-4 sm:p-5"><x-adminpanel::components.search-toolbar :action="route('master.users.index')" :value="request('q')" :per-page="$list->perPage()" :preserve="$activeFilters" :placeholder="__('admin.users.search')" /></div>
        <x-adminpanel::components.master.users.table :users="$list" />
        <div class="border-base-content/10 border-t px-4 py-4 sm:px-5"><x-adminpanel::components.pagination :paginator="$list" /></div>
    </section>
</x-adminpanel::layouts.app>
