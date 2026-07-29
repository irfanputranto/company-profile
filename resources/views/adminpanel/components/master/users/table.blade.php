@props(['users'])
<x-adminpanel::components.responsive-table>
    <table class="table min-w-[760px]">
        <thead><tr><th class="w-16">No.</th><th class="min-w-64">Pengguna</th><th class="w-40">Username</th><th class="w-40">Role</th><th class="w-28">Status</th><th class="sticky end-0 w-28 bg-base-100 text-end">Aksi</th></tr></thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="text-base-content/60">{{ $users->firstItem() + $loop->index }}</td>
                    <td><div class="flex items-center gap-3"><x-adminpanel::components.user-avatar :user="$user" size="sm" /><div><p class="font-medium">{{ $user->name }}</p><p class="text-base-content/50 text-xs">{{ $user->email }}</p></div></div></td>
                    <td>{{ $user->username }}</td>
                    <td><span class="badge badge-primary badge-soft">{{ str($user->roles->first()?->name ?? 'Tanpa role')->replace('_', ' ')->title() }}</span></td>
                    <td><span class="badge badge-sm badge-soft {{ $user->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $user->is_active ? 'Aktif' : 'Tidak aktif' }}</span></td>
                    <td class="sticky end-0 bg-base-100"><div class="flex justify-end gap-1">
                        @can('update_users')<a href="{{ route('master.users.edit', $user->uuid) }}" class="btn btn-square btn-text btn-sm" title="Edit pengguna"><span class="icon-[tabler--edit] size-5"></span></a>@endcan
                        @can('delete_users')<x-adminpanel::components.confirm-delete :action="route('master.users.destroy', $user->uuid)" :name="$user->name" title="Hapus pengguna?" />@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-14 text-center"><span class="icon-[tabler--users] text-base-content/30 mx-auto size-10"></span><p class="mt-3 font-medium">Data pengguna belum tersedia</p></td></tr>
            @endforelse
        </tbody>
    </table>
</x-adminpanel::components.responsive-table>
