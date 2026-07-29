@if ($languages->isNotEmpty())
    <div class="dropdown relative inline-flex [--offset:8] [--placement:bottom-end]">
        <button type="button"
            class="dropdown-toggle {{ $dark ? 'border-white/15 bg-white/5 text-white hover:bg-white/10' : 'btn-soft' }} btn btn-sm gap-2"
            aria-haspopup="menu" aria-expanded="false" aria-label="{{ __('company-profile.language_switcher.label') }}">
            <span class="icon-[tabler--language] size-5"></span>
            <span class="hidden sm:inline">{{ $languages->firstWhere('code', app()->getLocale())?->native_name ?? strtoupper(app()->getLocale()) }}</span>
            <span class="icon-[tabler--chevron-down] size-4"></span>
        </button>
        <ul class="dropdown-menu dropdown-open:opacity-100 hidden min-w-52 p-2" role="menu">
            @foreach ($languages as $language)
                <li>
                    <form action="{{ route('locale.switch', $language) }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item flex w-full items-center justify-between gap-3"
                            aria-current="{{ app()->getLocale() === $language->code ? 'true' : 'false' }}">
                            <span>{{ $language->native_name }}</span>
                            @if (app()->getLocale() === $language->code)
                                <span class="icon-[tabler--check] text-primary size-4"></span>
                            @endif
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endif
