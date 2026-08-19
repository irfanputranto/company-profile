@props(['blocks'])

<div {{ $attributes->merge(['class' => 'space-y-6 text-[1.02rem] leading-8 text-slate-600']) }}>
    @foreach($blocks as $block)
        @if($block['type'] === 'heading')
            @if(($block['level'] ?? 2) === 3)
                <h3 id="{{ $block['id'] }}" class="scroll-mt-28 pt-3 text-xl font-extrabold leading-snug text-[#17212b]">
                    {{ $block['text'] }}
                </h3>
            @else
                <h2 id="{{ $block['id'] }}" class="scroll-mt-28 pt-5 text-2xl font-extrabold leading-tight text-[#17212b] sm:text-3xl">
                    {{ $block['text'] }}
                </h2>
            @endif
        @elseif($block['type'] === 'ordered_list')
            <ol class="space-y-3 rounded-2xl border border-[#dcebea] bg-[#f5faf9] p-5 ps-11 marker:font-extrabold marker:text-[#078786]">
                @foreach($block['items'] as $item)
                    <li class="ps-1">{{ $item }}</li>
                @endforeach
            </ol>
        @elseif($block['type'] === 'unordered_list')
            <ul class="space-y-3 rounded-2xl border border-[#dcebea] bg-[#f5faf9] p-5 ps-11 marker:text-[#078786]">
                @foreach($block['items'] as $item)
                    <li class="ps-1">{{ $item }}</li>
                @endforeach
            </ul>
        @elseif($block['type'] === 'trusted_reference')
            @php
                $rawTitleIcon = $block['titleIcon'] ?? 'icon-[tabler--books]';
                $hasIconClass = is_string($rawTitleIcon) && str_starts_with(trim($rawTitleIcon), 'icon-[');
            @endphp
            <section class="rounded-3xl border border-[#b9d8d5] bg-[#edf6f5] p-6 sm:p-8">
                <div class="mb-6 flex items-start gap-4">
                    <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-[#078786] text-white">
                        @if($hasIconClass)
                            <span class="{{ trim($rawTitleIcon) }} size-6"></span>
                        @else
                            {{ $rawTitleIcon }}
                        @endif
                    </span>
                    <div>
                        <h2 class="text-xl font-extrabold text-[#17212b]">{{ $block['title'] ?? 'Referensi Tepercaya' }}</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $block['description'] ?? '' }}</p>
                    </div>
                </div>
                <div class="grid gap-3">
                    @foreach($block['sources'] as $source)
                        <a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer"
                            class="group flex items-center justify-between gap-4 rounded-2xl border border-[#dcebea] bg-white p-4 text-sm font-bold text-[#17212b] transition hover:-translate-y-0.5 hover:border-[#078786] hover:text-[#078786] hover:shadow-md">
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="rounded-xl bg-[#0aa8a7] text-[#fff] p-2 text-base">{{ $source['icon'] ?? '🔖' }}</span>
                                <span class="truncate">{{ $source['text'] }}</span>
                            </span>
                            <span aria-hidden="true">
                                <svg class="size-4 shrink-0 transition group-hover:translate-x-0.5 text-[#64748b]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                    <polyline points="15 3 21 3 21 9"></polyline>
                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @else
            <p>{{ $block['text'] }}</p>
        @endif
    @endforeach
</div>
