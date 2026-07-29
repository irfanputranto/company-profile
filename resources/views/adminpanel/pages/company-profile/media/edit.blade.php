<x-adminpanel::layouts.app :title="__('admin.media.edit_title')">
    <div class="mx-auto w-full max-w-3xl space-y-6">
        <x-adminpanel::components.page-header :title="__('admin.media.edit_title')" :description="__('admin.media.edit_description')" />

        <form method="POST" action="{{ route('company-profile.media.update', $row->uuid) }}" class="card shadow-md">
            @csrf
            @method('PUT')
            <div class="card-body space-y-5 p-5 sm:p-6">
                <img src="{{ $row->publicUrl() }}" alt="{{ $row->alt_text }}" class="h-56 w-full rounded-xl object-contain">
                <div>
                    <label class="label-text font-medium" for="alt_text">{{ __('admin.media.alt_text') }} <span class="text-error">*</span></label>
                    <input id="alt_text" name="alt_text" value="{{ old('alt_text', $row->alt_text) }}" class="input mt-2 w-full @error('alt_text') input-error @enderror" required>
                    @error('alt_text')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label-text font-medium" for="caption">{{ __('admin.media.caption') }}</label>
                    <textarea id="caption" name="caption" rows="4" class="textarea mt-2 w-full @error('caption') textarea-error @enderror">{{ old('caption', $row->caption) }}</textarea>
                    @error('caption')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="card-footer border-base-content/10 flex justify-end gap-3 border-t p-4 sm:px-6">
                <a href="{{ route('company-profile.media.index') }}" class="btn btn-text">{{ __('admin.common.cancel') }}</a>
                <button class="btn btn-primary">{{ __('admin.common.save_changes') }}</button>
            </div>
        </form>
    </div>
</x-adminpanel::layouts.app>
