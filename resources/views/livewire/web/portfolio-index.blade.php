<div class="space-y-5">
    <div class="flex justify-end gap-2">
        @foreach (['tr' => 'TR', 'en' => 'EN'] as $locale => $label)
            <button
                type="button"
                wire:click="setLocale('{{ $locale }}')"
                @class([
                    'cursor-pointer rounded-md px-3 py-1 text-[12px] font-bold',
                    'bg-accent text-white' => app()->getLocale() === $locale,
                    'bg-[var(--bg-card)] text-muted hover:text-ink' => app()->getLocale() !== $locale,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <header class="rounded-2xl border border-line bg-[var(--bg-card)] p-5 sm:p-7">
        <div class="flex items-center gap-2 text-[12px] font-bold uppercase tracking-[0.18em] text-accent">
            <i data-lucide="briefcase-business" class="h-4 w-4"></i>
            {{ __('Seçilmiş Çalışmalar') }}
        </div>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-ink sm:text-4xl">{{ __('Portfolyo') }}</h1>
        <p class="mt-2 max-w-2xl text-[14px] leading-relaxed text-muted sm:text-[15px]">
            {{ __('Ürün geliştirme, yönetim panelleri ve kullanıcı deneyimi odağında geliştirdiğim projeler.') }}
        </p>
    </header>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        @forelse ($projects as $project)
            @php
                $cover = $project->images->first();
                $technologies = collect($project->technologies ?? [])
                    ->map(fn ($slug) => $technologyCatalog[$slug] ?? null)
                    ->filter()
                    ->take(4);
            @endphp

            <article class="group flex h-full flex-col overflow-visible rounded-2xl border border-line bg-[var(--bg-card)] shadow-sm">
                <a href="{{ \App\Support\ReferenceUrl::route('portfolio.show', $project) }}" class="block">
                    <div class="relative aspect-[16/9] overflow-hidden rounded-t-2xl bg-soft">
                        @if ($cover)
                            <img
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.025]"
                                src="{{ asset($cover->path) }}"
                                alt="{{ $cover->title ?: $project->title }}"
                            />
                        @else
                            <div class="flex h-full flex-col items-center justify-center text-muted">
                                <i data-lucide="image-off" class="h-10 w-10"></i>
                                <span class="mt-2 text-xs font-semibold">{{ __('Görsel eklenmedi') }}</span>
                            </div>
                        @endif

                        @if ($project->is_featured)
                            <span class="absolute left-3 top-3 rounded-full bg-accent px-3 py-1 text-[10px] font-black uppercase tracking-wide text-white">
                                {{ __('Öne Çıkan') }}
                            </span>
                        @endif
                    </div>
                </a>

                <div class="flex-1 p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black text-ink">
                                <a href="{{ \App\Support\ReferenceUrl::route('portfolio.show', $project) }}" class="hover:text-accent">
                                    {{ $project->title }}
                                </a>
                            </h2>
                            <div class="mt-1 text-[11px] font-bold uppercase tracking-wide text-accent">
                                {{ $project->project_type }}
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-accentSoft px-2.5 py-1 text-[10px] font-bold text-accentDark">
                            {{ $project->project_date?->format('Y') ?? '-' }}
                        </span>
                    </div>

                    <p class="mt-3 line-clamp-3 text-[13px] leading-relaxed text-muted">
                        {{ $project->short_description }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($technologies as $technology)
                            @php
                                $technologyIcon = $technology->icon ?: 'code-2';
                                $technologyIcon = $technologyIcon === 'code-bracket' ? 'code-2' : $technologyIcon;
                            @endphp

                            <span
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-line bg-soft"
                                title="{{ $technology->name }}"
                            >
                                @if ($technology->logo_path)
                                    <img
                                        class="h-5 w-5 object-contain"
                                        src="{{ asset($technology->logo_path) }}"
                                        alt="{{ $technology->name }}"
                                    />
                                @else
                                    <i data-lucide="{{ $technologyIcon }}" class="h-4 w-4 text-accent"></i>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>

                <x-web.portfolio-link-buttons :project="$project" attached />
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-line bg-[var(--bg-card)] px-5 py-16 text-center">
                <i data-lucide="folder-open" class="mx-auto h-10 w-10 text-muted"></i>
                <h2 class="mt-3 text-lg font-black text-ink">{{ __('Henüz yayınlanmış proje yok') }}</h2>
            </div>
        @endforelse
    </section>
</div>
