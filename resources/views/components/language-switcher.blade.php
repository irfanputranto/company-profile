@if ($languages->isNotEmpty())
    @php
        $languageFlags = [
            'id' => '🇮🇩',
            'en' => '🇬🇧',
        ];
        $currentLanguage = $languages->firstWhere('code', app()->getLocale());
        $currentLanguageCode = $currentLanguage?->code ?? app()->getLocale();
    @endphp

    <div class="dropdown relative inline-flex [--offset:8] [--placement:bottom-end]"
        @if ($withFlags) data-public-language-switcher @endif>
        <button type="button"
            @class([
                'dropdown-toggle btn btn-sm gap-2',
                'border-white/15 bg-white/5 text-white hover:bg-white/10' => $dark,
                'btn-soft' => ! $dark && ! $withFlags,
                'rounded-full border border-[#dcebea] bg-white px-3 text-[#17212b] shadow-sm hover:border-[#0aa8a7]/50 hover:bg-[#edf6f5]' => ! $dark && $withFlags,
                'rounded-full px-3' => $dark && $withFlags,
            ])
            aria-haspopup="menu" aria-expanded="false" aria-label="{{ __('company-profile.language_switcher.label') }}">
            @if ($withFlags)
                <span class="text-lg leading-none" aria-hidden="true">{{ $languageFlags[$currentLanguageCode] ?? '🌐' }}</span>
                <span class="text-xs font-extrabold uppercase tracking-wide">{{ $currentLanguageCode }}</span>
            @else
                <span class="icon-[tabler--language] size-5"></span>
                <span class="hidden sm:inline">{{ $currentLanguage?->native_name ?? strtoupper($currentLanguageCode) }}</span>
            @endif
            <span class="icon-[tabler--chevron-down] size-4"></span>
        </button>
        <ul class="dropdown-menu dropdown-open:opacity-100 hidden min-w-52 p-2" role="menu">
            @foreach ($languages as $language)
                <li>
                    <form action="{{ route('locale.switch', $language) }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item flex w-full items-center justify-between gap-3"
                            data-language-code="{{ $language->code }}"
                            aria-current="{{ app()->getLocale() === $language->code ? 'true' : 'false' }}">
                            <span class="flex items-center gap-3">
                                @if ($withFlags)
                                    <span class="text-lg leading-none" aria-hidden="true">{{ $languageFlags[$language->code] ?? '🌐' }}</span>
                                @endif
                                <span>{{ $language->native_name }}</span>
                            </span>
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
