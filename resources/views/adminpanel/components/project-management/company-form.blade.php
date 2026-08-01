@props(['company' => null])
@php($field = fn (string $name) => old($name, data_get($company, $name)))

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2"><label for="name" class="label-text font-medium">{{ __('project-management.fields.company_name') }} *</label><input id="name" name="name" value="{{ $field('name') }}" class="input mt-2 w-full" required autofocus>@error('name')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror</div>
    <div><label for="contact_person" class="label-text font-medium">{{ __('project-management.fields.contact_person') }}</label><input id="contact_person" name="contact_person" value="{{ $field('contact_person') }}" class="input mt-2 w-full">@error('contact_person')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror</div>
    <div><label for="email" class="label-text font-medium">Email</label><input id="email" type="email" name="email" value="{{ $field('email') }}" class="input mt-2 w-full">@error('email')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror</div>
    <div><label for="phone" class="label-text font-medium">{{ __('project-management.fields.phone') }}</label><input id="phone" name="phone" value="{{ $field('phone') }}" class="input mt-2 w-full"></div>
    <div class="sm:col-span-2"><label for="address" class="label-text font-medium">{{ __('project-management.fields.address') }}</label><textarea id="address" name="address" class="textarea mt-2 min-h-24 w-full">{{ $field('address') }}</textarea></div>
    <div class="sm:col-span-2"><label for="notes" class="label-text font-medium">{{ __('project-management.fields.notes') }}</label><textarea id="notes" name="notes" class="textarea mt-2 min-h-28 w-full">{{ $field('notes') }}</textarea></div>
</div>
