@props(['row' => null, 'roles'])
@php($field = fn ($name, $default = null) => old($name, data_get($row, $name, $default)))
@php($selectedRole = old('role_id', $row?->roles?->first()?->id))

<div class="space-y-6">
    <x-adminpanel::components.master.users.photo-upload :user="$row" />

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="label-text font-medium" for="name">{{ __('admin.users.full_name') }} <span class="text-error">*</span></label>
            <input id="name" name="name" value="{{ $field('name') }}" class="input mt-2 w-full" required autofocus>
            @error('name')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label-text font-medium" for="username">Username <span class="text-error">*</span></label>
            <input id="username" name="username" value="{{ $field('username') }}" class="input mt-2 w-full" required>
            @error('username')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="label-text font-medium" for="email">Email <span class="text-error">*</span></label>
        <input id="email" type="email" name="email" value="{{ $field('email') }}" class="input mt-2 w-full" required>
        @error('email')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="label-text font-medium" for="password">{{ __('admin.users.password') }} {{ $row ? '' : '*' }}</label>
            <input id="password" type="password" name="password" class="input mt-2 w-full" {{ $row ? '' : 'required' }}>
            @if ($row)<p class="text-base-content/50 mt-1 text-xs">{{ __('admin.users.password_unchanged') }}</p>@endif
            @error('password')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label-text font-medium" for="password_confirmation">{{ __('admin.users.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="input mt-2 w-full" {{ $row ? '' : 'required' }}>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="label-text font-medium" for="role_id">{{ __('admin.roles.title') }} <span class="text-error">*</span></label>
            <div class="mt-2">
                <x-adminpanel::components.searchable-select id="role_id" name="role_id" :placeholder="__('admin.users.select_role')" :search-placeholder="__('admin.users.search_role')">
                    <option value="">{{ __('admin.users.select_role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) $selectedRole === (string) $role->id)>{{ str($role->name)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </x-adminpanel::components.searchable-select>
            </div>
            @error('role_id')<p class="text-error mt-1 text-sm">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label-text font-medium" for="is_active">{{ __('admin.profile.status') }}</label>
            <select id="is_active" name="is_active" class="select mt-2 w-full">
                <option value="1" @selected((string) $field('is_active', 1) === '1')>{{ __('admin.common.active') }}</option>
                <option value="0" @selected((string) $field('is_active', 1) === '0')>{{ __('admin.common.inactive') }}</option>
            </select>
        </div>
    </div>
</div>
