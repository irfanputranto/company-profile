@php
    $pageTitle = $page->translated('title');
    $pageDescription = $page->translated('content');
    $isLegal = $template === 'legal';
    $isLanding = $template === 'landing';
@endphp

<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks"
    :active-page="'page:'.$page->slug" :title="$pageTitle" :description="$pageDescription" :seo="$page->seoMetadata">
    <article>
        <header @class([
            'relative overflow-hidden border-b border-[#dcebea]',
            'bg-gradient-to-br from-[#e4f5f3] via-white to-[#f8fbfb] py-16 sm:py-24' => $isLanding,
            'bg-[#f7faf9] py-12 sm:py-16' => ! $isLanding,
        ])>
            @if($isLanding)
                <div class="pointer-events-none absolute -start-20 -top-20 size-80 rounded-full bg-[#0aa8a7]/15 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 end-0 size-96 rounded-full bg-[#078786]/10 blur-3xl"></div>
            @endif

            <div class="bs-container relative">
                <nav @class([
                    'flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500',
                    'mx-auto justify-center' => ! $isLegal,
                    'mx-auto max-w-4xl' => $isLegal,
                ]) aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="transition hover:text-[#078786]">{{ __('company-profile.public.navigation.home') }}</a>
                    <span class="icon-[tabler--chevron-right] size-3.5"></span>
                    <span class="text-[#078786]">{{ $pageTitle }}</span>
                </nav>

                <div @class([
                    'mt-7',
                    'mx-auto max-w-4xl text-center' => ! $isLegal,
                    'mx-auto max-w-4xl' => $isLegal,
                ]) data-reveal>
                    <span class="bs-kicker">{{ __('company-profile.public.pages.eyebrow') }}</span>
                    <h1 @class([
                        'bs-heading mt-6 leading-[1.08]',
                        'text-4xl sm:text-5xl lg:text-6xl' => $isLanding,
                        'text-4xl sm:text-5xl' => ! $isLanding,
                    ])>{{ $pageTitle }}</h1>
                    <p @class(['mt-5 text-sm text-slate-500', 'flex items-center justify-center gap-2' => ! $isLegal, 'flex items-center gap-2' => $isLegal])>
                        <span class="icon-[tabler--calendar-check] size-4 text-[#078786]"></span>
                        {{ __('company-profile.public.pages.updated', ['date' => $page->updated_at->translatedFormat('d F Y')]) }}
                    </p>
                </div>
            </div>
        </header>

        <section class="bs-section">
            <div class="bs-container">
                <div @class([
                    'mx-auto grid items-start gap-10',
                    'max-w-6xl lg:grid-cols-[minmax(0,1fr)_18rem]' => ! $isLegal,
                    'max-w-4xl' => $isLegal,
                ])>
                    <div class="min-w-0 rounded-3xl border border-[#dcebea] bg-white p-6 shadow-sm sm:p-9 lg:p-12">
                        <x-public.article-content :blocks="$formattedContent['blocks']" />

                        @if($formattedContent['sources'] !== [])
                            <div class="mt-10 border-t border-[#dcebea] pt-8">
                                <h2 class="flex items-center gap-2 text-lg font-extrabold text-[#17212b]">
                                    <span class="icon-[tabler--books] size-5 text-[#078786]"></span>
                                    {{ __('company-profile.public.blog.references') }}
                                </h2>
                                <div class="mt-4 grid gap-2">
                                    @foreach($formattedContent['sources'] as $source)
                                        <a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer"
                                            class="flex items-center justify-between gap-3 rounded-xl bg-[#edf6f5] px-4 py-3 text-sm font-bold text-[#078786] transition hover:bg-[#dff1ef]">
                                            <span class="truncate">{{ $source['label'] }}</span>
                                            <span class="icon-[tabler--external-link] size-4 shrink-0"></span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-10 border-t border-[#dcebea] pt-8">
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#078786]">
                                <span class="icon-[tabler--arrow-left] size-4"></span>
                                {{ __('company-profile.public.pages.back_home') }}
                            </a>
                        </div>
                    </div>

                    @if(! $isLegal && $formattedContent['headings'] !== [])
                        <aside class="rounded-2xl border border-[#dcebea] bg-[#f7faf9] p-5 lg:sticky lg:top-28">
                            <h2 class="flex items-center gap-2 text-sm font-extrabold text-[#17212b]">
                                <span class="icon-[tabler--list] size-5 text-[#078786]"></span>
                                {{ __('company-profile.public.pages.contents') }}
                            </h2>
                            <ol class="mt-4 space-y-3 border-s border-[#b9d8d5] ps-4">
                                @foreach($formattedContent['headings'] as $heading)
                                    <li>
                                        <a href="#{{ $heading['id'] }}" class="block text-sm leading-5 text-slate-500 transition hover:text-[#078786]">
                                            {{ $heading['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </aside>
                    @endif
                </div>
            </div>
        </section>
    </article>
</x-public.layout>
