@php($language ??= null)

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="label-text font-medium" for="code">{{ __('company-profile.languages.code') }}</label>
        <input class="input mt-2 w-full @error('code') input-error @enderror" id="code" name="code"
            value="{{ old('code', $language?->code) }}" placeholder="en" required>
        @error('code')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="label-text font-medium" for="name">{{ __('company-profile.languages.name') }}</label>
        <input class="input mt-2 w-full @error('name') input-error @enderror" id="name" name="name"
            value="{{ old('name', $language?->name) }}" placeholder="English" required>
        @error('name')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="label-text font-medium" for="native_name">{{ __('company-profile.languages.native_name') }}</label>
        <input class="input mt-2 w-full @error('native_name') input-error @enderror" id="native_name" name="native_name"
            value="{{ old('native_name', $language?->native_name) }}" placeholder="English" required>
        @error('native_name')<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="label-text font-medium" for="direction">{{ __('company-profile.languages.direction') }}</label>
        <select class="select mt-2 w-full" id="direction" name="direction" required>
            <option value="ltr" @selected(old('direction', $language?->direction ?? 'ltr') === 'ltr')>LTR</option>
            <option value="rtl" @selected(old('direction', $language?->direction) === 'rtl')>RTL</option>
        </select>
    </div>
    <div>
        <label class="label-text font-medium" for="sort_order">{{ __('company-profile.languages.sort_order') }}</label>
        <input class="input mt-2 w-full" id="sort_order" name="sort_order" type="number" min="0"
            value="{{ old('sort_order', $language?->sort_order ?? 0) }}" required>
    </div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        @foreach (['is_active' => __('company-profile.languages.active'), 'is_default' => __('company-profile.languages.default')] as $name => $label)
            <label class="border-base-content/20 flex items-center justify-between gap-3 rounded-lg border p-4" for="{{ $name }}">
                <span class="font-medium">{{ $label }}</span>
                <input type="hidden" name="{{ $name }}" value="0">
                <input class="switch switch-primary" id="{{ $name }}" name="{{ $name }}" type="checkbox" value="1"
                    @checked((bool) old($name, $language?->{$name} ?? ($name === 'is_active')))>
            </label>
        @endforeach
    </div>
</div>
