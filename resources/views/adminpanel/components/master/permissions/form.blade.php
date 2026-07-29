@props(['row' => null])
@php($isSystem = $row && in_array($row->name, \App\Modules\Master\Permission\Controllers\PermissionController::SYSTEM_PERMISSIONS, true))

<div class="space-y-5">
    @if ($isSystem)
        <div class="alert alert-warning alert-soft flex items-center gap-3"><span class="icon-[tabler--lock] size-5 shrink-0"></span><span class="text-sm">Permission sistem digunakan oleh aplikasi. Namanya tidak dapat diubah.</span></div>
    @endif
    <div>
        <label class="label-text font-medium" for="name">Nama permission <span class="text-error">*</span></label>
        <input id="name" name="name" value="{{ old('name', $row?->name) }}"
            class="input mt-2 w-full @error('name') input-error @enderror" placeholder="Contoh: view_articles"
            required autofocus @readonly($isSystem)>
        <p class="text-base-content/50 mt-1.5 text-xs">Gunakan format aksi dan resource, misalnya <span class="font-mono">view_articles</span>.</p>
        @error('name')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="label-text font-medium" for="guard_name">Guard</label>
        <div class="input bg-base-200/50 mt-2 flex w-full items-center gap-2"><span class="icon-[tabler--world] text-base-content/50 size-5"></span><span class="font-medium">web</span></div>
        <input id="guard_name" type="hidden" name="guard_name" value="web">
        <p class="text-base-content/50 mt-1.5 text-xs">Admin panel saat ini menggunakan guard web.</p>
    </div>
</div>
