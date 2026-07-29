<x-adminpanel::layouts.app :title="__('company-profile.languages.create')">
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <x-adminpanel::components.page-header :title="__('company-profile.languages.create')" :description="__('company-profile.languages.description')" />
        <form method="POST" action="{{ route('company-profile.languages.store') }}" class="card shadow-md">
            @csrf
            <div class="card-body p-5 sm:p-6">@include('adminpanel.pages.company-profile.languages._form')</div>
            <div class="card-footer border-base-content/10 flex justify-end gap-3 border-t p-4 sm:px-6">
                <a href="{{ route('company-profile.languages.index') }}" class="btn btn-text">{{ __('company-profile.actions.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('company-profile.actions.save') }}</button>
            </div>
        </form>
    </div>
</x-adminpanel::layouts.app>
