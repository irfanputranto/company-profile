@props([
    'name' => 'image',
    'id' => 'image',
    'label' => 'Unggah gambar',
    'description' => 'Gambar akan diperkecil dan dikompres otomatis.',
    'currentUrl' => null,
    'required' => false,
])

<div x-data="imageUpload(@js($currentUrl))" {{ $attributes->merge(['class' => 'rounded-xl border border-base-content/10 bg-base-200/30 p-4 sm:p-5']) }}>
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
        <div class="bg-base-100 relative flex size-28 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-base-content/10">
            <span x-show="!preview" class="icon-[tabler--photo] text-base-content/30 size-10"></span>
            <img x-show="preview" x-cloak :src="preview" alt="Pratinjau gambar" class="absolute inset-0 size-full object-contain p-2">
        </div>

        <div class="min-w-0 flex-1">
            <p class="font-medium">{{ $label }}</p>
            <p class="text-base-content/60 mt-1 text-sm">{{ $description }}</p>
            <label
                for="{{ $id }}"
                @dragenter.prevent="dragging = true"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="drop($event)"
                :class="dragging ? 'border-primary bg-primary/5' : 'border-base-content/20 hover:border-primary/60'"
                class="mt-3 flex cursor-pointer items-center justify-center gap-3 rounded-lg border-2 border-dashed px-4 py-4 text-center transition-colors"
            >
                <span class="icon-[tabler--cloud-upload] text-primary size-6 shrink-0"></span>
                <span>
                    <span class="block text-sm font-medium">Pilih atau tarik gambar</span>
                    <span class="text-base-content/50 block text-xs">JPG, PNG, WebP · maksimal 2 MB</span>
                </span>
                <input
                    x-ref="file"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="sr-only"
                    @change="choose($event.target.files[0])"
                    @required($required)
                >
            </label>
            <p class="text-base-content/50 mt-2 flex items-center gap-1.5 text-xs">
                <span class="icon-[tabler--sparkles] size-4 shrink-0"></span>
                Otomatis diubah menjadi WebP agar penyimpanan tetap ringan.
            </p>
            <p x-show="error" x-text="error" class="text-error mt-2 text-sm"></p>
            @error($name)
                <p class="text-error mt-2 text-sm">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
