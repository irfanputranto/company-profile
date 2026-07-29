@props(['user' => null])

<section class="space-y-3">
    <div>
        <h2 class="font-semibold">{{ __('admin.upload.profile_title') }}</h2>
        <p class="text-base-content/60 mt-1 text-sm">{{ __('admin.upload.profile_description') }}</p>
    </div>

    <div x-data="{
        preview: null,
        error: null,
        dragging: false,
        choose(file) {
            this.error = null;
            if (!file) return;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                this.error = @js(__('admin.upload.invalid_type'));
                this.$refs.photo.value = '';
                return;
            }
            if (file.size > {{ config('uploads.max_file_size_bytes') }}) {
                this.error = @js(__('admin.upload.too_large', ['size' => config('uploads.max_file_size_mb')]));
                this.$refs.photo.value = '';
                return;
            }
            if (this.preview) URL.revokeObjectURL(this.preview);
            this.preview = URL.createObjectURL(file);
        },
        drop(event) {
            this.dragging = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;
            const transfer = new DataTransfer();
            transfer.items.add(file);
            this.$refs.photo.files = transfer.files;
            this.choose(file);
        }
    }" class="rounded-xl border border-base-content/10 bg-base-200/30 p-4 sm:p-5">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="relative size-24 shrink-0">
                <x-adminpanel::components.user-avatar :user="$user" size="xl" class="size-24" />
                <img x-show="preview" x-cloak :src="preview" alt="{{ __('admin.upload.profile_preview') }}"
                    class="absolute inset-0 size-24 rounded-full border-4 border-base-100 object-cover shadow-md">
                <span class="bg-primary text-primary-content absolute end-0 bottom-0 flex size-8 items-center justify-center rounded-full border-2 border-base-100">
                    <span class="icon-[tabler--camera] size-4"></span>
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <label for="photo" @dragenter.prevent="dragging = true" @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false" @drop.prevent="drop($event)"
                    :class="dragging ? 'border-primary bg-primary/5' : 'border-base-content/20 hover:border-primary/60'"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-4 py-5 text-center transition-colors">
                    <span class="icon-[tabler--cloud-upload] text-primary size-7"></span>
                    <span class="mt-2 text-sm font-medium">{{ __('admin.upload.profile_choose') }}</span>
                    <span class="text-base-content/50 mt-1 text-xs">{{ __('admin.upload.formats', ['size' => config('uploads.max_file_size_mb')]) }}</span>
                    <input x-ref="photo" id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp"
                        class="sr-only" @change="choose($event.target.files[0])">
                </label>
                <p class="text-base-content/50 mt-2 flex items-center gap-1.5 text-xs"><span class="icon-[tabler--sparkles] size-4"></span>{{ __('admin.upload.profile_webp_hint') }}</p>
                <p x-show="error" x-text="error" class="text-error mt-2 text-sm"></p>
                @error('photo')<p class="text-error mt-2 text-sm">{{ $message }}</p>@enderror
            </div>
        </div>

        @if ($user?->avatar_path)
            <label class="mt-4 inline-flex cursor-pointer items-center gap-2 text-sm">
                <input type="checkbox" name="remove_photo" value="1" class="checkbox checkbox-error checkbox-sm">
                <span>{{ __('admin.upload.remove_current') }}</span>
            </label>
        @endif
    </div>
</section>
