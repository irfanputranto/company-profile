<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks" active-page="blog"
    :title="$article->translated('title')" :description="$article->translated('excerpt')">
    <article class="bs-section">
        <div class="bs-container">
            <header class="mx-auto max-w-3xl text-center" data-reveal>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#078786]">
                    <span class="icon-[tabler--arrow-left] size-4"></span>
                    {{ __('company-profile.public.blog.back') }}
                </a>
                <h1 class="bs-heading mt-6 text-4xl sm:text-5xl">{{ $article->translated('title') }}</h1>
                <div class="mt-5 flex flex-wrap justify-center gap-3 text-sm text-slate-500">
                    <time datetime="{{ $article->published_at?->toDateString() }}">
                        {{ $article->published_at?->translatedFormat('d F Y') }}
                    </time>
                    @if ($article->reading_time_minutes)
                        <span>· {{ __('company-profile.public.articles_section.minutes', ['value' => $article->reading_time_minutes]) }}</span>
                    @endif
                </div>
            </header>

            <img class="mx-auto mt-10 aspect-[16/7] w-full max-w-5xl rounded-2xl object-cover shadow-lg"
                src="{{ asset('vendor/bigspring/images/blog-'.(($article->id % 6) + 1).'.webp') }}"
                alt="" width="1080" height="472">

            <div class="mx-auto mt-10 max-w-3xl whitespace-pre-line text-base leading-8 text-slate-600">
                {{ $article->translated('content') }}
            </div>
        </div>
    </article>
</x-public.layout>
