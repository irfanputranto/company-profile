@props(['users'])
<x-adminpanel::components.responsive-table>
    <table class="table min-w-[760px]">
        <thead><tr><th class="w-16">No.</th><th class="min-w-64">{{ __('admin.users.title') }}</th><th class="w-40">Username</th><th class="w-40">{{ __('admin.roles.title') }}</th><th class="w-28">{{ __('admin.profile.status') }}</th><th class="sticky end-0 w-28 bg-base-100 text-end">{{ __('admin.common.actions') }}</th></tr></thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="text-base-content/60">{{ $users->firstItem() + $loop->index }}</td>
                    <td><div class="flex items-center gap-3"><x-adminpanel::components.user-avatar :user="$user" size="sm" /><div><p class="font-medium">{{ $user->name }}</p><p class="text-base-content/50 text-xs">{{ $user->email }}</p></div></div></td>
                    <td>{{ $user->username }}</td>
                    <td><span class="badge badge-primary badge-soft">{{ str($user->roles->first()?->name ?? __('admin.users.no_role'))->replace('_', ' ')->title() }}</span></td>
                    <td><span class="badge badge-sm badge-soft {{ $user->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $user->is_active ? __('admin.common.active') : __('admin.common.inactive') }}</span></td>
                    <td class="sticky end-0 bg-base-100"><div class="flex justify-end gap-1">
                        @can('update_users')<a href="{{ route('master.users.edit', $user->uuid) }}" class="btn btn-square btn-text btn-sm" title="{{ __('admin.users.edit_action') }}"><span class="icon-[tabler--edit] size-5"></span></a>@endcan
                        @can('delete_users')<x-adminpanel::components.confirm-delete :action="route('master.users.destroy', $user->uuid)" :name="$user->name" :title="__('admin.users.delete_title')" />@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-14 text-center"><span class="icon-[tabler--users] text-base-content/30 mx-auto size-10"></span><p class="mt-3 font-medium">{{ __('admin.users.empty') }}</p></td></tr>
            @endforelse
        </tbody>
    </table>
</x-adminpanel::components.responsive-table>
