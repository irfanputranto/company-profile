<x-adminpanel::layouts.app :title="__('company-profile.translations.title')">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <x-adminpanel::components.page-header
            :title="__('company-profile.translations.title').' — '.$definition['singular'].' #'.$model->getKey()"
            :description="__('company-profile.translations.description')" />
        <x-adminpanel::components.flash-message />

        <form method="POST" action="{{ route('company-profile.translations.update', ['resource' => $resource, 'record' => $model->getKey()]) }}" class="card shadow-md">
            @csrf
            @method('PUT')
            <div class="card-body grid grid-cols-1 gap-5 p-5 lg:grid-cols-2 sm:p-6">
                @foreach ($languages as $language)
                    <fieldset class="border-base-content/20 space-y-4 rounded-xl border p-4">
                        <legend class="px-2 font-semibold">
                            {{ $language->native_name }}
                            @if ($language->is_default)<span class="badge badge-success badge-soft ms-2">{{ __('company-profile.languages.default') }}</span>@endif
                        </legend>
                        @foreach ($fields as $field)
                            @php
                                $translation = $model->contentTranslations
                                    ->first(fn ($item) => $item->language_id === $language->id && $item->field === $field['name']);
                                $inputName = "translations.{$language->code}.{$field['name']}";
                                $value = old($inputName, $translation?->value);
                            @endphp
                            <div>
                                <label class="label-text font-medium" for="translation-{{ $language->code }}-{{ $field['name'] }}">{{ $field['label'] }}</label>
                                @if ($field['type'] === 'textarea')
                                    <textarea class="textarea mt-2 w-full @error($inputName) textarea-error @enderror"
                                        id="translation-{{ $language->code }}-{{ $field['name'] }}"
                                        name="translations[{{ $language->code }}][{{ $field['name'] }}]" rows="5"
                                        placeholder="{{ data_get($model, $field['name']) }}">{{ $value }}</textarea>
                                @else
                                    <input class="input mt-2 w-full @error($inputName) input-error @enderror"
                                        id="translation-{{ $language->code }}-{{ $field['name'] }}"
                                        name="translations[{{ $language->code }}][{{ $field['name'] }}]"
                                        value="{{ $value }}" placeholder="{{ data_get($model, $field['name']) }}">
                                @endif
                                @error($inputName)<p class="text-error mt-1.5 text-sm">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </fieldset>
                @endforeach
            </div>
            <div class="card-footer border-base-content/10 flex justify-end gap-3 border-t p-4 sm:px-6">
                <a href="{{ route('company-profile.content.index', ['resource' => $resource]) }}" class="btn btn-text">{{ __('company-profile.actions.cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('company-profile.translations.save') }}</button>
            </div>
        </form>
    </div>
</x-adminpanel::layouts.app>
