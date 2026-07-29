<x-adminpanel::layouts.app :title="__('admin.media.upload_title')">
    <div class="mx-auto w-full max-w-3xl space-y-6">
        <x-adminpanel::components.page-header :title="__('admin.media.upload_title')" :description="__('admin.media.upload_description')" />

        <form method="POST" action="{{ route('company-profile.media.store') }}" enctype="multipart/form-data" class="card shadow-md">
            @csrf
            <div class="card-body space-y-5 p-5 sm:p-6">
                <x-adminpanel::components.image-upload name="image" id="image" :label="__('admin.media.content_image')" :required="true" />
                <div>
                    <label class="label-text font-medium" for="alt_text">{{ __('admin.media.alt_text') }} <span class="text-error">*</span></label>
                    <input id="alt_text" name="alt_text" value="{{ old('alt_text') }}" class="input mt-2 w-full @error('alt_text') input-error @enderror" required>
                    @error('alt_text')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label-text font-medium" for="caption">{{ __('admin.media.caption') }}</label>
                    <textarea id="caption" name="caption" rows="4" class="textarea mt-2 w-full @error('caption') textarea-error @enderror">{{ old('caption') }}</textarea>
                    @error('caption')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="card-footer border-base-content/10 flex justify-end gap-3 border-t p-4 sm:px-6">
                <a href="{{ route('company-profile.media.index') }}" class="btn btn-text">{{ __('admin.common.cancel') }}</a>
                <button class="btn btn-primary">{{ __('admin.media.upload_convert') }}</button>
            </div>
        </form>
    </div>
</x-adminpanel::layouts.app>
