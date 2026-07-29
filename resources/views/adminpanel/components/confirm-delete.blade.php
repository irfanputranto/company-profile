@props([
    'action',
    'name' => 'data ini',
    'title' => 'Hapus data?',
    'description' => null,
    'buttonLabel' => null,
])

@php($dialogTitleId = 'confirm-delete-' . md5($action))

<div x-data="confirmDelete" {{ $attributes->merge(['class' => 'inline-flex']) }}>
    <button type="button" class="btn btn-text btn-error btn-sm {{ $buttonLabel ? 'gap-2' : 'btn-square' }}" @click="show"
        title="Hapus {{ $name }}" aria-label="Hapus {{ $name }}">
        <span class="icon-[tabler--trash] size-5"></span>
        @if ($buttonLabel)<span>{{ $buttonLabel }}</span>@endif
    </button>

    <template x-teleport="body">
        <div x-cloak x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            role="dialog" aria-modal="true" aria-labelledby="{{ $dialogTitleId }}" @keydown.escape.window="close">
            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/55 backdrop-blur-[2px]" @click="close"></div>

            <div x-show="open" x-transition
                class="bg-base-100 relative z-10 w-full max-w-md rounded-2xl p-6 text-center shadow-2xl">
                <div class="bg-error/10 text-error mx-auto flex size-16 items-center justify-center rounded-full">
                    <span class="icon-[tabler--trash] size-8"></span>
                </div>

                <h2 id="{{ $dialogTitleId }}" class="text-base-content mt-5 text-xl font-semibold">{{ $title }}</h2>
                <p class="text-base-content/60 mt-2 text-sm leading-6">
                    {{ $description ?? "Anda akan menghapus {$name}. Tindakan ini tidak dapat dibatalkan." }}
                </p>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center">
                    <button type="button" class="btn btn-soft min-w-28" @click="close">Batal</button>
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error min-w-28">
                            <span class="icon-[tabler--trash] size-5"></span>
                            Ya, hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
