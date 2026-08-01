<x-adminpanel::layouts.app :title="__('project-management.servers.credentials')">
    <div class="mx-auto max-w-3xl space-y-6">
        <x-adminpanel::components.page-header :title="__('project-management.servers.credentials')" :description="$managedProject->name.' · '.$server->name">
            <x-slot:actions><a href="{{ route('project-management.projects.show', $managedProject) }}#servers" class="btn btn-text"><span class="icon-[tabler--arrow-left] size-5"></span>{{ __('admin.common.back') }}</a></x-slot:actions>
        </x-adminpanel::components.page-header>
        <div class="alert alert-warning"><span class="icon-[tabler--shield-lock] size-5"></span><span>{{ __('project-management.servers.credentials_warning') }}</span></div>
        <section class="card shadow-md"><div class="card-body space-y-5 p-5 sm:p-6">
            @foreach([
                __('project-management.fields.username') => $server->username,
                __('project-management.fields.password') => $server->password,
                'API Key / Token' => $server->api_secret,
                __('project-management.fields.credential_notes') => $server->credentials_note,
            ] as $label => $value)
                <div><p class="text-base-content/60 text-sm">{{ $label }}</p><pre class="bg-base-200 mt-2 overflow-x-auto whitespace-pre-wrap break-all rounded-lg p-4 text-sm">{{ $value ?: '—' }}</pre></div>
            @endforeach
        </div></section>
    </div>
</x-adminpanel::layouts.app>
