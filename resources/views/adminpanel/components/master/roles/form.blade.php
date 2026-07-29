@props(['row' => null, 'permissions', 'permissionGroups'])

@php
    $selectedPermissionIds = collect(old('permission_ids', $row?->permissions?->pluck('id')->all() ?? []))->map(fn ($id) => (string) $id)->values();
    $allPermissionIds = $permissions->pluck('id')->map(fn ($id) => (string) $id)->values();
@endphp

<div class="space-y-7" x-data="{
    query: '',
    selected: @js($selectedPermissionIds),
    allPermissions: @js($allPermissionIds),
    matches(name) { return name.toLowerCase().includes(this.query.toLowerCase().trim()) },
    selectAll() { this.selected = [...this.allPermissions] },
    clearAll() { this.selected = [] }
}">
    <section class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="label-text font-medium" for="name">Nama role <span class="text-error">*</span></label>
            <input id="name" name="name" value="{{ old('name', $row?->name) }}"
                class="input mt-2 w-full @error('name') input-error @enderror" placeholder="Contoh: supervisor kasir" required autofocus>
            <p class="text-base-content/50 mt-1.5 text-xs">Nama akan disimpan dalam format huruf kecil.</p>
            @error('name')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label-text font-medium">Guard</label>
            <div class="input bg-base-200/50 mt-2 flex w-full items-center gap-2"><span class="icon-[tabler--world] text-base-content/50 size-5"></span><span class="font-medium">web</span></div>
            <input type="hidden" name="guard_name" value="web">
        </div>
    </section>

    <section class="space-y-4 border-base-content/10 border-t pt-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div><h2 class="font-semibold">Permission role</h2><p class="text-base-content/60 mt-1 text-sm">Satu role dapat memiliki banyak permission.</p></div>
            <span class="badge badge-primary badge-soft self-start sm:self-auto"><span x-text="selected.length"></span>&nbsp;dipilih</span>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <label class="input flex w-full items-center gap-2 sm:max-w-md">
                <span class="icon-[tabler--search] text-base-content/50 size-5 shrink-0"></span>
                <input x-model="query" type="search" class="grow" placeholder="Cari permission...">
            </label>
            <div class="flex gap-2"><button type="button" class="btn btn-soft btn-sm" x-on:click="selectAll()">Pilih semua</button><button type="button" class="btn btn-text btn-sm" x-on:click="clearAll()">Kosongkan</button></div>
        </div>

        @error('permission_ids')<p class="text-error text-sm">{{ $message }}</p>@enderror
        @error('permission_ids.*')<p class="text-error text-sm">{{ $message }}</p>@enderror

        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($permissionGroups as $group => $groupPermissions)
                <div class="rounded-xl border border-base-content/10 bg-base-200/20 p-4">
                    <div class="mb-3 flex items-center gap-2"><span class="icon-[tabler--folder] text-primary size-5"></span><h3 class="font-semibold capitalize">{{ $group }}</h3><span class="badge badge-sm badge-neutral badge-soft">{{ $groupPermissions->count() }}</span></div>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($groupPermissions as $permission)
                            <label x-show="matches(@js($permission->name))" class="hover:border-primary/40 flex cursor-pointer items-start gap-3 rounded-lg border border-base-content/10 bg-base-100 p-3 transition-colors">
                                <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" x-model="selected" class="checkbox checkbox-primary checkbox-sm mt-0.5">
                                <span class="min-w-0"><span class="block break-words font-mono text-xs font-semibold">{{ $permission->name }}</span><span class="text-base-content/50 mt-0.5 block text-xs">{{ str($permission->name)->replace(['_', '-', '.'], ' ')->title() }}</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
