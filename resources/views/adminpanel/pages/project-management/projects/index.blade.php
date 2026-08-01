<x-adminpanel::layouts.app :title="__('project-management.projects.title')">
    <x-adminpanel::components.page-header :title="__('project-management.projects.title')" :description="__('project-management.projects.description')">
        <x-slot:actions><div class="flex gap-2">@can('view_client_companies')<a href="{{ route('project-management.companies.index') }}" class="btn btn-soft"><span class="icon-[tabler--building] size-5"></span>{{ __('project-management.navigation.companies') }}</a>@endcan @can('create_managed_projects')<a href="{{ route('project-management.projects.create') }}" class="btn btn-primary"><span class="icon-[tabler--plus] size-5"></span>{{ __('project-management.projects.add') }}</a>@endcan</div></x-slot:actions>
    </x-adminpanel::components.page-header>
    <x-adminpanel::components.flash-message />
    <section class="card overflow-hidden shadow-md">
        <form method="GET" class="border-base-content/10 grid gap-3 border-b p-4 sm:grid-cols-2 lg:grid-cols-[1fr_13rem_15rem_auto]">
            <input name="q" value="{{ request('q') }}" class="input w-full" placeholder="{{ __('project-management.projects.search') }}">
            <select name="status" class="select w-full"><option value="">{{ __('project-management.fields.all_status') }}</option>@foreach(__('project-management.status.projects') as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
            <select name="company" class="select w-full"><option value="">{{ __('project-management.fields.all_companies') }}</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string)request('company') === (string)$company->id)>{{ $company->name }}</option>@endforeach</select>
            <button class="btn btn-primary">{{ __('admin.common.filter') }}</button>
        </form>
        <x-adminpanel::components.responsive-table>
            <table class="table"><thead><tr><th>{{ __('project-management.fields.project') }}</th><th>{{ __('project-management.fields.company') }}</th><th>Status</th><th>{{ __('project-management.fields.timeline') }}</th><th>{{ __('project-management.fields.data') }}</th><th class="text-end">{{ __('admin.common.actions') }}</th></tr></thead>
                <tbody>@forelse($projects as $project)<tr><td><a href="{{ route('project-management.projects.show', $project) }}" class="link link-primary font-semibold">{{ $project->name }}</a><p class="text-base-content/60 text-xs">{{ $project->code }}</p></td><td>{{ $project->clientCompany->name }}</td><td><span class="badge badge-soft">{{ __('project-management.status.projects.'.$project->status) }}</span></td><td><p class="text-sm">{{ $project->started_at?->isoFormat('D MMM YYYY') ?: '—' }}</p><p class="text-base-content/60 text-xs">{{ __('project-management.fields.until') }} {{ $project->due_at?->isoFormat('D MMM YYYY') ?: '—' }}</p></td><td><span class="text-xs">{{ $project->phases_count }} {{ __('project-management.phases.short') }} · {{ $project->documents_count }} {{ __('project-management.documents.short') }}</span></td><td><div class="flex justify-end gap-2"><a href="{{ route('project-management.projects.show', $project) }}" class="btn btn-soft btn-square btn-sm"><span class="icon-[tabler--eye] size-4"></span></a>@can('update_managed_projects')<a href="{{ route('project-management.projects.edit', $project) }}" class="btn btn-soft btn-square btn-sm"><span class="icon-[tabler--edit] size-4"></span></a>@endcan</div></td></tr>@empty<tr><td colspan="6" class="py-10 text-center">{{ __('project-management.projects.empty') }}</td></tr>@endforelse</tbody>
            </table>
        </x-adminpanel::components.responsive-table>
        <div class="border-base-content/10 border-t p-4"><x-adminpanel::components.pagination :paginator="$projects" /></div>
    </section>
</x-adminpanel::layouts.app>
