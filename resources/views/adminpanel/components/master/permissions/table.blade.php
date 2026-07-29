@props(['permissions'])

<x-adminpanel::components.responsive-table>
    <table class="table min-w-[850px]">
        <thead>
            <tr>
                <th class="w-16">No.</th>
                <th class="min-w-80">Permission</th>
                <th class="w-32">Guard</th>
                <th class="w-36 text-center">Role</th>
                <th class="w-36 text-center">Pengguna</th>
                <th class="w-36">Jenis</th>
                <th class="sticky end-0 w-28 bg-base-100 text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $permission)
                @php
                    $isSystem = in_array($permission->name, \App\Modules\Master\Permission\Controllers\PermissionController::systemPermissions(), true);
                    $isAssigned = ($permission->roles_count + $permission->users_count) > 0;
                @endphp
                <tr>
                    <td class="text-base-content/60">{{ $permissions->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <span class="{{ $isSystem ? 'bg-primary/10 text-primary' : 'bg-base-200 text-base-content/60' }} flex size-9 shrink-0 items-center justify-center rounded-lg">
                                <span class="icon-[tabler--key] size-5"></span>
                            </span>
                            <div>
                                <p class="font-mono text-sm font-semibold">{{ $permission->name }}</p>
                                <p class="text-base-content/50 text-xs">{{ str($permission->name)->replace(['_', '-', '.'], ' ')->title() }}</p>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-neutral badge-soft">{{ $permission->guard_name }}</span></td>
                    <td class="text-center"><span class="font-semibold">{{ $permission->roles_count }}</span></td>
                    <td class="text-center"><span class="font-semibold">{{ $permission->users_count }}</span></td>
                    <td><span class="badge badge-sm badge-soft {{ $isSystem ? 'badge-primary' : 'badge-neutral' }}">{{ $isSystem ? 'Sistem' : 'Tambahan' }}</span></td>
                    <td class="sticky end-0 bg-base-100">
                        <div class="flex justify-end gap-1">
                            @if ($isSystem || $isAssigned)
                                <span class="btn btn-square btn-text btn-sm cursor-default text-base-content/30"
                                    title="Permission yang digunakan dikunci">
                                    <span class="icon-[tabler--lock] size-5"></span>
                                </span>
                            @else
                                @can('update_permissions')
                                <a href="{{ route('master.permissions.edit', $permission->id) }}"
                                    class="btn btn-square btn-text btn-sm" title="Edit permission" aria-label="Edit permission {{ $permission->name }}">
                                    <span class="icon-[tabler--edit] size-5"></span>
                                </a>
                                @endcan
                                @can('delete_permissions')
                                <x-adminpanel::components.confirm-delete :action="route('master.permissions.destroy', $permission->id)"
                                    :name="$permission->name" title="Hapus permission?" />
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-14 text-center"><span class="icon-[tabler--shield-lock] text-base-content/30 mx-auto size-10"></span><p class="mt-3 font-medium">Data permission belum tersedia</p><p class="text-base-content/50 mt-1 text-sm">Tambahkan permission untuk memulai.</p></td></tr>
            @endforelse
        </tbody>
    </table>
</x-adminpanel::components.responsive-table>
