<x-adminpanel::layouts.app :title="__('project-management.companies.title')">
    <x-adminpanel::components.page-header :title="__('project-management.companies.title')" :description="__('project-management.companies.description')">
        <x-slot:actions>@can('create_client_companies')<a href="{{ route('project-management.companies.create') }}" class="btn btn-primary"><span class="icon-[tabler--plus] size-5"></span>{{ __('project-management.companies.add') }}</a>@endcan</x-slot:actions>
    </x-adminpanel::components.page-header>
    <x-adminpanel::components.flash-message />
    <section class="card overflow-hidden shadow-md">
        <form method="GET" class="border-base-content/10 flex gap-3 border-b p-4"><input name="q" value="{{ request('q') }}" class="input w-full" placeholder="{{ __('project-management.companies.search') }}"><button class="btn btn-primary">{{ __('admin.common.filter') }}</button></form>
        <x-adminpanel::components.responsive-table>
            <table class="table"><thead><tr><th>{{ __('project-management.fields.company_name') }}</th><th>{{ __('project-management.fields.contact_person') }}</th><th>{{ __('project-management.fields.projects') }}</th><th class="text-end">{{ __('admin.common.actions') }}</th></tr></thead>
                <tbody>@forelse($companies as $company)<tr><td><p class="font-semibold">{{ $company->name }}</p><p class="text-base-content/60 text-xs">{{ $company->email ?: '—' }}</p></td><td>{{ $company->contact_person ?: '—' }}</td><td><span class="badge badge-soft badge-primary">{{ $company->managed_projects_count }}</span></td><td><div class="flex justify-end gap-2">@can('update_client_companies')<a href="{{ route('project-management.companies.edit', $company) }}" class="btn btn-soft btn-square btn-sm" title="{{ __('admin.common.save_changes') }}"><span class="icon-[tabler--edit] size-4"></span></a>@endcan @can('delete_client_companies')<form method="POST" action="{{ route('project-management.companies.destroy', $company) }}" onsubmit="return confirm(@js(__('project-management.companies.delete_confirm')))" >@csrf @method('DELETE')<button class="btn btn-soft btn-square btn-error btn-sm"><span class="icon-[tabler--trash] size-4"></span></button></form>@endcan</div></td></tr>@empty<tr><td colspan="4" class="py-10 text-center">{{ __('project-management.companies.empty') }}</td></tr>@endforelse</tbody>
            </table>
        </x-adminpanel::components.responsive-table>
        <div class="border-base-content/10 border-t p-4"><x-adminpanel::components.pagination :paginator="$companies" /></div>
    </section>
</x-adminpanel::layouts.app>
