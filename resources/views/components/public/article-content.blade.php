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
        @else
            <p>{{ $block['text'] }}</p>
        @endif
    @endforeach
</div>
