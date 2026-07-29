<x-adminpanel::layouts.app :title="__('admin.crud.edit_title', ['resource' => $resourceDefinition['singular']])">
    <div class="mx-auto w-full max-w-5xl space-y-6">
        <x-adminpanel::components.page-header
            :title="__('admin.crud.edit_title', ['resource' => $resourceDefinition['singular']])"
            :description="__('admin.crud.edit_description', ['resource' => $resourceDefinition['singular']])" />

        <form method="POST"
            action="{{ route('company-profile.content.update', ['resource' => $resourceKey, 'record' => $row->id]) }}"
            class="card shadow-md">
            @csrf
            @method('PUT')
            <div class="card-body p-5 sm:p-6">
                <x-adminpanel::components.company-profile.crud-form :definition="$resourceDefinition" :row="$row" />
            </div>
            <div class="card-footer border-base-content/10 flex justify-end gap-3 border-t p-4 sm:px-6">
                <a href="{{ route('company-profile.content.index', ['resource' => $resourceKey]) }}" class="btn btn-text">{{ __('admin.common.cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('admin.common.save_changes') }}</button>
            </div>
        </form>
    </div>
</x-adminpanel::layouts.app>
