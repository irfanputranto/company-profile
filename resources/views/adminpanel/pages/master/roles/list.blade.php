<x-adminpanel::layouts.app title="Master Role">
    <x-adminpanel::components.page-header title="Role" :actions-first="true" description="Kelola role dan kumpulan permission untuk setiap jenis pengguna."><x-slot:actions>@can('create_roles')<a href="{{ route('master.roles.create') }}" class="btn btn-primary"><span class="icon-[tabler--plus] size-5"></span>Tambah role</a>@endcan</x-slot:actions></x-adminpanel::components.page-header>
    <x-adminpanel::components.flash-message />
    <section class="card shadow-base-300/10 overflow-hidden shadow-md">
        <div class="bg-base-200/40 border-base-content/10 border-b p-4 sm:p-5"><x-adminpanel::components.dynamic-filters :action="route('master.roles.index')" :filters="$filters" :preserve="['q' => request('q'), 'limit' => $list->perPage()]" /></div>
        <div class="card-body border-base-content/10 border-b p-4 sm:p-5"><x-adminpanel::components.search-toolbar :action="route('master.roles.index')" :value="request('q')" :per-page="$list->perPage()" :preserve="$activeFilters" placeholder="Cari role atau permission" /></div>
        <x-adminpanel::components.master.roles.table :roles="$list" />
        <div class="border-base-content/10 border-t px-4 py-4 sm:px-5"><x-adminpanel::components.pagination :paginator="$list" /></div>
    </section>
</x-adminpanel::layouts.app>
