@php
    $articleTitle = $article->translated('title');
    $articleExcerpt = $article->translated('excerpt');
    $categoryName = $article->category?->translated('name') ?? __('company-profile.public.blog.category_fallback');
    $authorName = $profile?->user_id === $article->author_id
        ? $profile->public_name
        : ($article->author?->name ?? $profile?->public_name ?? config('app.name'));
    $authorWords = collect(explode(' ', $authorName))->filter()->take(2);
    $authorInitials = $authorWords->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');
    $articleUrl = route('blog.show', $article);
    $shareText = rawurlencode($articleTitle.' — '.$articleUrl);
    $shareUrl = rawurlencode($articleUrl);
@endphp

<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks" active-page="blog"
    :title="$articleTitle" :description="$articleExcerpt" :seo="$article->seoMetadata" open-graph-type="article">
    <article>
        <header class="relative overflow-hidden border-b border-[#dcebea] bg-gradient-to-b from-[#edf8f7] via-white to-white py-12 sm:py-16">
            <div class="pointer-events-none absolute -start-24 top-5 size-72 rounded-full bg-[#0aa8a7]/10 blur-3xl"></div>
            <div class="bs-container relative">
                <nav class="mx-auto flex max-w-4xl flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="transition hover:text-[#078786]">{{ __('company-profile.public.navigation.home') }}</a>
                    <span class="icon-[tabler--chevron-right] size-3.5"></span>
                    <a href="{{ route('blog.index') }}" class="transition hover:text-[#078786]">{{ __('company-profile.public.navigation.blog') }}</a>
                    <span class="icon-[tabler--chevron-right] size-3.5"></span>
                    <span class="text-[#078786]">{{ $categoryName }}</span>
                </nav>

                <div class="mx-auto mt-7 max-w-4xl text-center" data-reveal>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <span class="bs-kicker">{{ $categoryName }}</span>
                        @if($article->is_featured)
                            <span class="rounded-full bg-[#17212b] px-3 py-1 text-[0.68rem] font-extrabold uppercase tracking-[0.14em] text-white">
                                {{ __('company-profile.public.articles_section.eyebrow') }}
                            </span>
                        @endif
                    </div>

                    <h1 class="bs-heading mx-auto mt-6 max-w-4xl text-4xl leading-[1.08] sm:text-5xl lg:text-6xl">
                        {{ $articleTitle }}
                    </h1>

                    @if($articleExcerpt)
                        <p class="mx-auto mt-6 max-w-3xl text-base leading-8 text-slate-600 sm:text-xl sm:leading-9">
                            {{ $articleExcerpt }}
                        </p>
                    @endif

                    <div class="mt-7 flex flex-wrap items-center justify-center gap-x-5 gap-y-3 text-sm text-slate-500">
                        <div class="flex items-center gap-2.5">
                            <span class="grid size-9 place-items-center rounded-full bg-[#078786] text-xs font-extrabold text-white shadow-sm">
                                {{ $authorInitials }}
                            </span>
                            <span><span class="text-slate-400">{{ __('company-profile.public.blog.by') }}</span> <strong class="text-[#17212b]">{{ $authorName }}</strong></span>
                        </div>
                        <span class="hidden size-1 rounded-full bg-slate-300 sm:block"></span>
                        <time class="flex items-center gap-1.5" datetime="{{ $article->published_at?->toDateString() }}">
                            <span class="icon-[tabler--calendar] size-4"></span>
                            {{ $article->published_at?->translatedFormat('d F Y') }}
                        </time>
                        @if($article->reading_time_minutes)
                            <span class="flex items-center gap-1.5">
                                <span class="icon-[tabler--clock] size-4"></span>
                                {{ __('company-profile.public.articles_section.minutes', ['value' => $article->reading_time_minutes]) }}
                            </span>
                        @endif
                    </div>

                    @if($article->tags->isNotEmpty())
                        <div class="mt-6 flex flex-wrap justify-center gap-2">
                            @foreach($article->tags as $tag)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-[#b9d8d5] bg-white px-3 py-1.5 text-xs font-bold text-[#078786] shadow-sm">
                                    <span class="icon-[tabler--hash] size-3.5"></span>{{ $tag->translated('name') }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <div class="bs-container -mt-1 pb-20">
            <figure class="relative mx-auto max-w-6xl overflow-hidden rounded-3xl border-8 border-white bg-white shadow-2xl shadow-slate-900/15" data-reveal>
                <img class="aspect-[16/7] w-full object-cover"
                    src="{{ asset('vendor/bigspring/images/blog-'.(($article->id % 6) + 1).'.webp') }}"
                    alt="{{ $articleTitle }}" width="1200" height="525">
                <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/75 to-transparent px-6 pb-5 pt-16 text-xs font-semibold text-white/80">
                    {{ $categoryName }} · {{ $article->published_at?->translatedFormat('Y') }}
                </figcaption>
            </figure>

            <div class="mx-auto mt-14 grid max-w-6xl gap-12 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-start">
                <div class="min-w-0">
                    <x-public.article-content :blocks="$formattedContent['blocks']" />

                    @if($formattedContent['sources'] !== [])
                        <section class="mt-12 rounded-3xl border border-[#b9d8d5] bg-[#edf6f5] p-6 sm:p-8" aria-labelledby="article-references">
                            <div class="flex items-start gap-4">
                                <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-[#078786] text-white">
                                    <span class="icon-[tabler--books] size-6"></span>
                                </span>
                                <div>
                                    <h2 id="article-references" class="text-xl font-extrabold text-[#17212b]">{{ __('company-profile.public.blog.references') }}</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ __('company-profile.public.blog.references_description') }}</p>
                                </div>
                            </div>
                            <div class="mt-6 grid gap-3">
                                @foreach($formattedContent['sources'] as $source)
                                    <a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer"
                                        class="group flex items-center justify-between gap-4 rounded-2xl border border-[#dcebea] bg-white p-4 text-sm font-bold text-[#17212b] transition hover:-translate-y-0.5 hover:border-[#078786] hover:text-[#078786] hover:shadow-md">
                                        <span class="flex min-w-0 items-center gap-3">
                                            <span class="icon-[tabler--world] size-5 shrink-0 text-[#078786]"></span>
                                            <span class="truncate">{{ $source['label'] }}</span>
                                        </span>
                                        <span class="icon-[tabler--external-link] size-4 shrink-0 transition group-hover:translate-x-0.5"></span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="mt-10 flex flex-col gap-5 rounded-3xl bg-[#17212b] p-6 text-white sm:flex-row sm:items-center sm:justify-between sm:p-8">
                        <div class="flex items-center gap-4">
                            <span class="grid size-14 shrink-0 place-items-center rounded-full bg-[#0aa8a7] text-lg font-extrabold">{{ $authorInitials }}</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.15em] text-[#75d8d4]">{{ __('company-profile.public.blog.by') }}</p>
                                <h2 class="mt-1 text-lg font-extrabold">{{ $authorName }}</h2>
                                <p class="mt-1 max-w-xl text-sm leading-6 text-slate-300">{{ __('company-profile.public.blog.author_note') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('about') }}" class="inline-flex shrink-0 items-center gap-2 text-sm font-bold text-[#75d8d4] hover:text-white">
                            {{ __('company-profile.public.navigation.about') }}
                            <span class="icon-[tabler--arrow-up-right] size-4"></span>
                        </a>
                    </section>
                </div>

                <aside class="space-y-5 lg:sticky lg:top-28">
                    @if($formattedContent['headings'] !== [])
                        <nav class="rounded-2xl border border-[#dcebea] bg-white p-5 shadow-sm" aria-labelledby="article-toc">
                            <h2 id="article-toc" class="flex items-center gap-2 text-sm font-extrabold text-[#17212b]">
                                <span class="icon-[tabler--list] size-5 text-[#078786]"></span>
                                {{ __('company-profile.public.blog.table_of_contents') }}
                            </h2>
                            <ol class="mt-4 space-y-3 border-s border-[#dcebea] ps-4">
                                @foreach($formattedContent['headings'] as $heading)
                                    <li>
                                        <a href="#{{ $heading['id'] }}" class="block text-sm leading-5 text-slate-500 transition hover:text-[#078786]">
                                            {{ $heading['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif

                    <div class="rounded-2xl border border-[#dcebea] bg-[#edf6f5] p-5">
                        <h2 class="text-sm font-extrabold text-[#17212b]">{{ __('company-profile.public.blog.share') }}</h2>
                        <div class="mt-4 grid gap-2">
                            <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:shadow-md">
                                <span class="icon-[tabler--brand-whatsapp] size-5"></span>
                                {{ __('company-profile.public.blog.share_whatsapp') }}
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#0A66C2] px-4 py-2.5 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:shadow-md">
                                <span class="icon-[tabler--brand-linkedin] size-5"></span>
                                {{ __('company-profile.public.blog.share_linkedin') }}
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('blog.index') }}" class="flex items-center justify-center gap-2 rounded-2xl border border-[#b9d8d5] bg-white px-4 py-3 text-sm font-bold text-[#078786] transition hover:bg-[#edf6f5]">
                        <span class="icon-[tabler--arrow-left] size-4"></span>
                        {{ __('company-profile.public.blog.back') }}
                    </a>
                </aside>
            </div>
        </div>

        @if($relatedArticles->isNotEmpty())
            <section class="border-t border-[#dcebea] bg-[#edf6f5] py-16 sm:py-20">
                <div class="bs-container">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <span class="bs-kicker">{{ $categoryName }}</span>
                            <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.blog.related') }}</h2>
                            <p class="mt-3 text-slate-600">{{ __('company-profile.public.blog.related_description') }}</p>
                        </div>
                        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#078786]">
                            {{ __('company-profile.public.blog.back') }}
                            <span class="icon-[tabler--arrow-right] size-4"></span>
                        </a>
                    </div>

                    <div class="mt-9 grid gap-6 md:grid-cols-3">
                        @foreach($relatedArticles as $relatedArticle)
                            <article class="overflow-hidden rounded-2xl border border-[#dcebea] bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                <img class="aspect-video w-full object-cover"
                                    src="{{ asset('vendor/bigspring/images/blog-'.(($relatedArticle->id % 6) + 1).'.webp') }}"
                                    alt="{{ $relatedArticle->translated('title') }}" loading="lazy" width="540" height="304">
                                <div class="p-5">
                                    <div class="flex items-center gap-2 text-xs font-bold text-[#078786]">
                                        <span>{{ $relatedArticle->category?->translated('name') ?? $categoryName }}</span>
                                        <span class="size-1 rounded-full bg-[#b9d8d5]"></span>
                                        <time class="text-slate-500">{{ $relatedArticle->published_at?->translatedFormat('d M Y') }}</time>
                                    </div>
                                    <h3 class="mt-3 text-lg font-extrabold leading-snug text-[#17212b]">
                                        <a href="{{ route('blog.show', $relatedArticle) }}" class="hover:text-[#078786]">
                                            {{ $relatedArticle->translated('title') }}
                                        </a>
                                    </h3>
                                    <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ $relatedArticle->translated('excerpt') }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>
</x-public.layout>
