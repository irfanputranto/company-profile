<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks" active-page="blog"
    :title="__('company-profile.public.blog.meta_title')"
    :description="__('company-profile.public.blog.meta_description')">
    <section class="bs-section">
        <div class="bs-container">
            <div class="mx-auto max-w-3xl text-center" data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.blog.eyebrow') }}</span>
                <h1 class="bs-heading mt-5 text-4xl sm:text-5xl">{{ __('company-profile.public.blog.title') }}</h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-8 sm:text-lg">
                    {{ __('company-profile.public.blog.description') }}
                </p>
            </div>

            @if ($articles->isNotEmpty())
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($articles as $article)
                        <article class="bs-project-card flex flex-col" data-reveal>
                            <img src="{{ asset('vendor/bigspring/images/blog-'.(($loop->index % 6) + 1).'.webp') }}"
                                alt="" loading="lazy" width="540" height="280">
                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex flex-wrap gap-3 text-xs font-semibold text-slate-500">
                                    <time datetime="{{ $article->published_at?->toDateString() }}">
                                        {{ $article->published_at?->translatedFormat('d M Y') }}
                                    </time>
                                    @if ($article->reading_time_minutes)
                                        <span>· {{ __('company-profile.public.articles_section.minutes', ['value' => $article->reading_time_minutes]) }}</span>
                                    @endif
                                </div>
                                <h2 class="mt-3 text-xl font-extrabold leading-snug text-[#17212b]">
                                    <a class="hover:text-[#078786]" href="{{ route('blog.show', $article) }}">
                                        {{ $article->translated('title') }}
                                    </a>
                                </h2>
                                <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500">{{ $article->translated('excerpt') }}</p>
                                <a class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#078786]"
                                    href="{{ route('blog.show', $article) }}">
                                    {{ __('company-profile.public.blog.read_more') }}
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">{{ $articles->links() }}</div>
            @else
                <div class="mt-12 rounded-2xl border border-dashed border-[#b9d8d5] bg-[#edf6f5] p-10 text-center">
                    <p>{{ __('company-profile.public.blog.empty') }}</p>
                </div>
            @endif
        </div>
    </section>
</x-public.layout>
