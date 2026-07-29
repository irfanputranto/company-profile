@props(['projects'])

@if ($projects->isNotEmpty())
    @php($images = ['blog-1.webp', 'blog-2.webp', 'blog-3.webp', 'blog-4.webp', 'blog-5.webp', 'blog-6.webp'])

    <section id="projects" class="bs-section bs-section-light" data-nav-section="projects">
        <div class="bs-container">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.projects_section.eyebrow') }}</span>
                <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.projects_section.title') }}</h2>
                <p class="mt-4 leading-7">{{ __('company-profile.public.projects_section.description') }}</p>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <article class="bs-project-card" data-reveal>
                        <img src="{{ asset('vendor/bigspring/images/'.$images[$loop->index % count($images)]) }}"
                            alt="" loading="lazy" width="540" height="280">
                        <div class="p-6">
                            <h3 class="text-xl font-extrabold text-[#17212b]">{{ $project->translated('title') }}</h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500">{{ $project->translated('summary') }}</p>
                            @if ($project->skills->isNotEmpty())
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($project->skills->take(4) as $skill)
                                        <span class="badge badge-soft badge-primary badge-sm">{{ $skill->translated('name') }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if ($project->project_url || $project->repository_url)
                                <div class="mt-5 flex flex-wrap gap-4 text-sm font-bold text-[#078786]">
                                    @if ($project->project_url)
                                        <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer">{{ __('company-profile.public.projects_section.view') }}</a>
                                    @endif
                                    @if ($project->repository_url)
                                        <a href="{{ $project->repository_url }}" target="_blank" rel="noopener noreferrer">{{ __('company-profile.public.projects_section.repository') }}</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
