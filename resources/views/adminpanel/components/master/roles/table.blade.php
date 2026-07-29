@props(['roles'])

<x-adminpanel::components.responsive-table>
    <table class="table min-w-[950px]">
        <thead><tr><th class="w-16">No.</th><th class="min-w-56">{{ __('admin.roles.title') }}</th><th class="min-w-96">{{ __('admin.permissions.title') }}</th><th class="w-32 text-center">{{ __('admin.roles.permission_count') }}</th><th class="w-32 text-center">{{ __('admin.roles.user_count') }}</th><th class="w-32">{{ __('admin.roles.kind') }}</th><th class="sticky end-0 w-28 bg-base-100 text-end">{{ __('admin.common.actions') }}</th></tr></thead>
        <tbody>
            @forelse ($roles as $role)
                @php($isSystem = in_array($role->name, \App\Modules\Master\Role\Controllers\RoleController::SYSTEM_ROLES, true))
                <tr>
                    <td class="text-base-content/60">{{ $roles->firstItem() + $loop->index }}</td>
                    <td><div class="flex items-center gap-3"><span class="{{ $isSystem ? 'bg-primary/10 text-primary' : 'bg-base-200 text-base-content/60' }} flex size-9 shrink-0 items-center justify-center rounded-lg"><span class="icon-[tabler--user-shield] size-5"></span></span><div><p class="font-semibold">{{ str($role->name)->replace('_', ' ')->title() }}</p><p class="text-base-content/50 font-mono text-xs">{{ $role->name }}</p></div></div></td>
                    <td><div class="flex max-w-xl flex-wrap gap-1">@foreach ($role->permissions->take(4) as $permission)<span class="badge badge-sm badge-soft badge-neutral">{{ $permission->name }}</span>@endforeach @if ($role->permissions_count > 4)<span class="badge badge-sm badge-soft badge-primary">+{{ $role->permissions_count - 4 }}</span>@endif @if ($role->permissions_count === 0)<span class="text-base-content/40">{{ __('admin.roles.no_permissions') }}</span>@endif</div></td>
                    <td class="text-center font-semibold">{{ $role->permissions_count }}</td>
                    <td class="text-center font-semibold">{{ $role->users_count }}</td>
                    <td><span class="badge badge-sm badge-soft {{ $isSystem ? 'badge-primary' : 'badge-neutral' }}">{{ $isSystem ? __('admin.common.system') : __('admin.common.operational') }}</span></td>
                    <td class="sticky end-0 bg-base-100"><div class="flex justify-end gap-1">@if ($isSystem)<span class="btn btn-square btn-text btn-sm cursor-default text-base-content/30" title="{{ __('admin.roles.system_locked') }}"><span class="icon-[tabler--lock] size-5"></span></span>@else
@can('update_roles')<a href="{{ route('master.roles.edit', $role->id) }}" class="btn btn-square btn-text btn-sm" title="{{ __('admin.roles.edit_action') }}" aria-label="{{ __('admin.roles.edit_action') }} {{ $role->name }}"><span class="icon-[tabler--edit] size-5"></span></a>@endcan
@if ($role->users_count === 0)@can('delete_roles')<x-adminpanel::components.confirm-delete :action="route('master.roles.destroy', $role->id)" :name="$role->name" :title="__('admin.roles.delete_title')" />@endcan
@else<span class="btn btn-square btn-text btn-sm cursor-default text-base-content/30" title="{{ __('admin.roles.in_use') }}"><span class="icon-[tabler--trash-off] size-5"></span></span>@endif @endif</div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-14 text-center"><span class="icon-[tabler--user-shield] text-base-content/30 mx-auto size-10"></span><p class="mt-3 font-medium">{{ __('admin.roles.empty') }}</p><p class="text-base-content/50 mt-1 text-sm">{{ __('admin.roles.empty_description') }}</p></td></tr>
            @endforelse
        </tbody>
    </table>
</x-adminpanel::components.responsive-table>
