@props(['articles'])

@if ($articles->isNotEmpty())
    @php($images = ['blog-4.webp', 'blog-5.webp', 'blog-6.webp'])

    <section id="articles" class="bs-section">
        <div class="bs-container">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.articles_section.eyebrow') }}</span>
                <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.articles_section.title') }}</h2>
                <p class="mt-4 leading-7">{{ __('company-profile.public.articles_section.description') }}</p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($articles as $article)
                    <article class="overflow-hidden rounded-2xl border border-[#dcebea] bg-white shadow-sm" data-reveal>
                        <img class="aspect-video w-full object-cover" src="{{ asset('vendor/bigspring/images/'.$images[$loop->index % count($images)]) }}"
                            alt="" loading="lazy" width="540" height="270">
                        <div class="p-6">
                            <div class="flex flex-wrap gap-3 text-xs font-semibold text-slate-500">
                                <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->translatedFormat('d M Y') }}</time>
                                @if ($article->reading_time_minutes)
                                    <span>· {{ __('company-profile.public.articles_section.minutes', ['value' => $article->reading_time_minutes]) }}</span>
                                @endif
                            </div>
                            <h3 class="mt-3 text-xl font-extrabold leading-snug text-[#17212b]">
                                <a class="transition-colors hover:text-[#078786]" href="{{ route('blog.show', $article) }}">
                                    {{ $article->translated('title') }}
                                </a>
                            </h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500">{{ $article->translated('excerpt') }}</p>
                            <a class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#078786]"
                                href="{{ route('blog.show', $article) }}">
                                {{ __('company-profile.public.articles_section.read') }}
                                <span class="icon-[tabler--arrow-up-right] size-4"></span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
