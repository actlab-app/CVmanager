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

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2 text-[12px] font-bold uppercase tracking-[0.18em] text-accent">
                <i data-lucide="folder-kanban" class="h-4 w-4"></i>
                {{ __('Seçilmiş Proje') }}
            </div>
            <h1 class="text-3xl font-black tracking-tight text-ink sm:text-4xl">{{ $project->title }}</h1>
            <p class="mt-2 max-w-2xl text-[14px] leading-relaxed text-muted sm:text-[15px]">
                {{ $project->short_description }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="rounded-full border border-line bg-[var(--bg-card)] px-3 py-1.5 text-[11px] font-bold text-muted">
                {{ $project->project_date?->format('Y') ?? '-' }}
            </span>
            <span class="rounded-full bg-accentSoft px-3 py-1.5 text-[11px] font-bold text-accentDark">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1.65fr)_minmax(320px,0.75fr)]">
        <div
            x-data="{
                active: 0,
                total: {{ $project->images->count() }},
                lightbox: false,
                magnifier: {
                    visible: false,
                    x: 0,
                    y: 0,
                    size: 300,
                    zoom: 2,
                    backgroundImage: '',
                    backgroundSize: '',
                    backgroundPosition: '',
                },
                previous() {
                    this.hideMagnifier();
                    this.active = (this.active - 1 + this.total) % this.total;
                },
                next() {
                    this.hideMagnifier();
                    this.active = (this.active + 1) % this.total;
                },
                openLightbox() {
                    this.lightbox = true;
                    this.$nextTick(() => {
                        this.$refs.lightboxClose?.focus();
                        window.lucide?.createIcons();
                    });
                },
                closeLightbox() {
                    this.hideMagnifier();
                    this.lightbox = false;
                    this.$nextTick(() => this.$refs.lightboxTrigger?.focus());
                },
                canMagnify() {
                    return window.matchMedia('(min-width: 1024px) and (hover: hover) and (pointer: fine)').matches;
                },
                updateMagnifier(event, imageUrl) {
                    if (! this.canMagnify()) {
                        this.hideMagnifier();
                        return;
                    }

                    const image = event.currentTarget;
                    const imageBox = image.getBoundingClientRect();
                    const stageBox = this.$refs.lightboxStage.getBoundingClientRect();
                    const imageRatio = image.naturalWidth / image.naturalHeight;
                    const boxRatio = imageBox.width / imageBox.height;
                    const renderedWidth = imageRatio > boxRatio
                        ? imageBox.width
                        : imageBox.height * imageRatio;
                    const renderedHeight = imageRatio > boxRatio
                        ? imageBox.width / imageRatio
                        : imageBox.height;
                    const imageLeft = imageBox.left + (imageBox.width - renderedWidth) / 2;
                    const imageTop = imageBox.top + (imageBox.height - renderedHeight) / 2;
                    const imageX = event.clientX - imageLeft;
                    const imageY = event.clientY - imageTop;

                    if (imageX < 0 || imageY < 0 || imageX > renderedWidth || imageY > renderedHeight) {
                        this.hideMagnifier();
                        return;
                    }

                    this.magnifier.x = event.clientX - stageBox.left;
                    this.magnifier.y = event.clientY - stageBox.top;
                    this.magnifier.backgroundImage = `url(${JSON.stringify(imageUrl)})`;
                    this.magnifier.backgroundSize = `${renderedWidth * this.magnifier.zoom}px ${renderedHeight * this.magnifier.zoom}px`;
                    this.magnifier.backgroundPosition = `${(this.magnifier.size / 2) - (imageX * this.magnifier.zoom)}px ${(this.magnifier.size / 2) - (imageY * this.magnifier.zoom)}px`;
                    this.magnifier.visible = true;
                },
                hideMagnifier() {
                    this.magnifier.visible = false;
                }
            }"
            x-effect="document.body.style.overflow = lightbox ? 'hidden' : ''"
            x-on:keydown.escape.window="if (lightbox) closeLightbox()"
            x-on:keydown.arrow-left.window="if (lightbox && total > 1) previous()"
            x-on:keydown.arrow-right.window="if (lightbox && total > 1) next()"
            x-on:livewire:navigating.window="document.body.style.overflow = ''"
            class="overflow-hidden rounded-2xl border border-line bg-[var(--bg-card)] shadow-lg">
            @if ($project->images->isNotEmpty())
                <div class="relative aspect-[16/10] overflow-hidden bg-soft">
                    @foreach ($project->images as $index => $image)
                        <img
                            x-show="active === {{ $index }}"
                            x-transition.opacity.duration.300ms
                            class="absolute inset-0 h-full w-full object-cover"
                            src="{{ asset($image->path) }}"
                            alt="{{ $image->title }}"
                            @if ($index !== 0) style="display: none;" @endif
                        />
                    @endforeach

                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/45 to-transparent p-5 pr-16 pt-16 text-white">
                        @foreach ($project->images as $index => $image)
                            <div x-show="active === {{ $index }}" @if ($index !== 0) style="display: none;" @endif>
                                <div class="text-[15px] font-black sm:text-[17px]">{{ $image->title }}</div>
                                <div class="mt-1 max-w-2xl text-[12px] leading-relaxed text-white/75 sm:text-[13px]">
                                    {{ $image->description }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($project->images->count() > 1)
                        <button type="button" x-on:click="previous()"
                            class="absolute left-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-white/30 bg-slate-950/45 text-white backdrop-blur transition hover:bg-slate-950/70"
                            aria-label="{{ __('Önceki görsel') }}">
                            <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        </button>
                        <button type="button" x-on:click="next()"
                            class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-white/30 bg-slate-950/45 text-white backdrop-blur transition hover:bg-slate-950/70"
                            aria-label="{{ __('Sonraki görsel') }}">
                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                        </button>
                    @endif

                    <button
                        type="button"
                        x-ref="lightboxTrigger"
                        x-on:click="openLightbox()"
                        class="absolute bottom-4 right-4 z-10 flex h-10 w-10 items-center justify-center rounded-full border border-white/30 bg-slate-950/55 text-white shadow-lg backdrop-blur transition hover:scale-105 hover:bg-slate-950/80 focus:outline-none focus:ring-2 focus:ring-white/80"
                        aria-label="{{ __('Tam ekran görüntüle') }}"
                        title="{{ __('Tam ekran görüntüle') }}"
                    >
                        <i data-lucide="maximize-2" class="h-4.5 w-4.5"></i>
                    </button>
                </div>

                <div class="flex items-center justify-between gap-4 border-t border-line px-4 py-3">
                    <div class="flex gap-1.5">
                        @foreach ($project->images as $index => $image)
                            <button type="button" x-on:click="active = {{ $index }}"
                                class="h-1.5 rounded-full transition-all"
                                x-bind:class="active === {{ $index }} ? 'w-7 bg-accent' : 'w-2 bg-line'"
                                aria-label="{{ $image->title }}"></button>
                        @endforeach
                    </div>
                    <span class="flex items-center gap-1.5 text-[11px] font-semibold text-muted">
                        <i data-lucide="images" class="h-3.5 w-3.5"></i>
                        {{ $project->images->count() }} {{ __('ürün görünümü') }}
                    </span>
                </div>

                <template x-teleport="body">
                    <div
                        x-show="lightbox"
                        x-transition.opacity.duration.200ms
                        x-on:click.self="closeLightbox()"
                        class="fixed inset-0 z-[200] bg-slate-950/95 p-3 text-white backdrop-blur-sm sm:p-6"
                        role="dialog"
                        aria-modal="true"
                        aria-label="{{ __('Proje görsel galerisi') }}"
                        style="display: none;"
                    >
                        <button
                            type="button"
                            x-ref="lightboxClose"
                            x-on:click="closeLightbox()"
                            class="absolute right-4 top-4 z-30 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-black/40 text-white backdrop-blur transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/80 sm:right-6 sm:top-6"
                            aria-label="{{ __('Galeriyi kapat') }}"
                        >
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>

                        <div class="mx-auto flex h-full max-w-[1600px] flex-col">
                            <div
                                x-ref="lightboxStage"
                                class="relative min-h-0 flex-1 overflow-hidden"
                            >
                                @foreach ($project->images as $index => $image)
                                    <img
                                        x-show="active === {{ $index }}"
                                        x-transition.opacity.duration.250ms
                                        x-on:mousemove="updateMagnifier($event, @js(asset($image->path)))"
                                        x-on:mouseleave="hideMagnifier()"
                                        class="absolute inset-0 h-full w-full object-contain lg:cursor-crosshair"
                                        src="{{ asset($image->path) }}"
                                        alt="{{ $image->title }}"
                                        @if ($index !== 0) style="display: none;" @endif
                                    />
                                @endforeach

                                <div
                                    x-show="magnifier.visible"
                                    x-bind:style="{
                                        left: `${magnifier.x - (magnifier.size / 2)}px`,
                                        top: `${magnifier.y - (magnifier.size / 2)}px`,
                                        width: `${magnifier.size}px`,
                                        height: `${magnifier.size}px`,
                                        backgroundImage: magnifier.backgroundImage,
                                        backgroundSize: magnifier.backgroundSize,
                                        backgroundPosition: magnifier.backgroundPosition,
                                    }"
                                    class="pointer-events-none absolute z-30 hidden rounded-xl border-2 border-white/80 bg-no-repeat shadow-2xl ring-1 ring-black/40 lg:block"
                                    aria-hidden="true"
                                    style="display: none;"
                                ></div>

                                @if ($project->images->count() > 1)
                                    <button
                                        type="button"
                                        x-on:click="previous()"
                                        class="absolute left-0 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/45 text-white backdrop-blur transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/80 sm:left-3 sm:h-12 sm:w-12"
                                        aria-label="{{ __('Önceki görsel') }}"
                                    >
                                        <i data-lucide="chevron-left" class="h-5 w-5"></i>
                                    </button>
                                    <button
                                        type="button"
                                        x-on:click="next()"
                                        class="absolute right-0 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/45 text-white backdrop-blur transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/80 sm:right-3 sm:h-12 sm:w-12"
                                        aria-label="{{ __('Sonraki görsel') }}"
                                    >
                                        <i data-lucide="chevron-right" class="h-5 w-5"></i>
                                    </button>
                                @endif
                            </div>

                            <div class="mx-auto w-full max-w-5xl shrink-0 pt-4 sm:pt-5">
                                <div class="rounded-2xl border border-white/10 bg-black/35 p-4 backdrop-blur-md sm:p-5">
                                    @foreach ($project->images as $index => $image)
                                        <div x-show="active === {{ $index }}" @if ($index !== 0) style="display: none;" @endif>
                                            <div class="text-base font-black sm:text-lg">{{ $image->title }}</div>
                                            @if ($image->description)
                                                <div class="mt-1 max-w-3xl text-xs leading-relaxed text-white/70 sm:text-sm">
                                                    {{ $image->description }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach

                                    <div class="mt-3 flex items-center justify-between gap-4 border-t border-white/10 pt-3">
                                        <div class="flex gap-1.5">
                                            @foreach ($project->images as $index => $image)
                                                <button
                                                    type="button"
                                                    x-on:click="active = {{ $index }}"
                                                    class="h-1.5 rounded-full transition-all"
                                                    x-bind:class="active === {{ $index }} ? 'w-8 bg-white' : 'w-2 bg-white/30 hover:bg-white/60'"
                                                    aria-label="{{ $image->title }}"
                                                ></button>
                                            @endforeach
                                        </div>
                                        <span class="text-xs font-bold text-white/60">
                                            <span x-text="active + 1"></span> / {{ $project->images->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            @else
                <div class="flex aspect-[16/10] flex-col items-center justify-center bg-soft text-muted">
                    <i data-lucide="image-off" class="h-10 w-10"></i>
                    <span class="mt-2 text-sm font-semibold">{{ __('Proje görseli eklenmedi') }}</span>
                </div>
            @endif
        </div>

        <aside class="flex flex-col rounded-2xl border border-line bg-[var(--bg-card)] p-4 shadow-sm sm:p-5">
            <div class="flex items-center gap-3 border-b border-line pb-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-accentSoft text-accent">
                    <i data-lucide="panels-top-left" class="h-5 w-5"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-muted">{{ __('Proje Türü') }}</div>
                    <div class="mt-0.5 text-[15px] font-black text-ink">{{ $project->project_type }}</div>
                </div>
            </div>

            <dl class="divide-y divide-line text-[13px]">
                <div class="grid grid-cols-[92px_1fr] gap-3 py-3">
                    <dt class="font-bold text-muted">{{ __('Rol') }}</dt>
                    <dd class="font-semibold text-ink">{{ $project->role }}</dd>
                </div>
                <div class="grid grid-cols-[92px_1fr] gap-3 py-3">
                    <dt class="font-bold text-muted">{{ __('Süre') }}</dt>
                    <dd class="font-semibold text-ink">{{ $project->duration }}</dd>
                </div>
                <div class="grid grid-cols-[92px_1fr] gap-3 py-3">
                    <dt class="font-bold text-muted">{{ __('Durum') }}</dt>
                    <dd class="font-semibold text-ink">{{ $statusLabel }}</dd>
                </div>
                <div class="grid grid-cols-[92px_1fr] gap-3 py-3">
                    <dt class="font-bold text-muted">{{ __('Platform') }}</dt>
                    <dd class="font-semibold text-ink">{{ $project->platform }}</dd>
                </div>
                <div class="grid grid-cols-[92px_1fr] gap-3 py-3">
                    <dt class="font-bold text-muted">{{ __('Dil') }}</dt>
                    <dd class="font-semibold text-ink">{{ __('Türkçe / English') }}</dd>
                </div>
            </dl>

            <div class="mt-auto grid grid-cols-2 gap-2 pt-4">
                @if ($project->live_url)
                <a
                    href="{{ $project->live_url }}"
                    class="flex items-center justify-center gap-2 rounded-xl bg-accent px-3 py-2.5 text-[12px] font-bold text-white transition hover:bg-accentDark"
                    target="_blank"
                    rel="noreferrer"
                >
                    <i data-lucide="external-link" class="h-4 w-4"></i>
                    {{ __('Canlı Görünüm') }}
                </a>
                @endif
                @if ($project->repository_url)
                <a
                    href="{{ $project->repository_url }}"
                    target="_blank"
                    rel="noreferrer"
                    class="flex items-center justify-center gap-2 rounded-xl border border-line bg-soft px-3 py-2.5 text-[12px] font-bold text-ink transition hover:border-accent"
                >
                    <i data-lucide="github" class="h-4 w-4"></i>
                    {{ __('Kaynak Kod') }}
                </a>
                @else
                <span
                    class="flex cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-line bg-soft px-3 py-2.5 text-[12px] font-bold text-muted opacity-70"
                    aria-disabled="true"
                    title="{{ __('Repository bağlantısı eklenmedi') }}"
                >
                    <i data-lucide="github" class="h-4 w-4"></i>
                    {{ __('Kaynak Kod') }}
                </span>
                @endif
            </div>
        </aside>
    </section>

    @if ($project->detailed_description)
        <section class="rounded-2xl border border-line bg-[var(--bg-card)] p-4 sm:p-5">
            <div class="mb-3 flex items-center gap-2 text-[13px] font-black text-accentDark">
                <i data-lucide="align-left" class="h-4 w-4"></i>
                {{ __('PROJE HAKKINDA') }}
            </div>
            <p class="text-[13px] leading-relaxed text-muted sm:text-[14px]">{{ $project->detailed_description }}</p>
        </section>
    @endif

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <div class="rounded-2xl border border-line bg-[var(--bg-card)] p-4 sm:p-5">
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-accentSoft px-3 py-2 text-[13px] font-black text-accentDark">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--bg-card)]">
                    <i data-lucide="layers-3" class="h-4 w-4"></i>
                </span>
                {{ __('KULLANILAN TEKNOLOJİLER') }}
            </div>

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @forelse ($technologies as $technology)
                    <div class="flex min-w-0 flex-col items-center rounded-xl border border-line bg-soft p-3 text-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--bg-card)]">
                            @if ($technology->logo_path)
                                <img
                                    class="h-5 w-5 object-contain"
                                    src="{{ asset($technology->logo_path) }}"
                                    alt=""
                                />
                            @else
                                <i data-lucide="{{ $technology->icon ?: 'code-bracket' }}" class="h-5 w-5 text-accent"></i>
                            @endif
                        </div>
                        <div class="mt-2 truncate text-[12px] font-extrabold text-ink">{{ $technology->name }}</div>
                        <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-muted">
                            {{ $technology->category }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-sm text-muted">{{ __('Teknoloji seçilmedi.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-[var(--bg-card)] p-4 sm:p-5">
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-accentSoft px-3 py-2 text-[13px] font-black text-accentDark">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--bg-card)]">
                    <i data-lucide="milestone" class="h-4 w-4"></i>
                </span>
                {{ __('TEKNİK KARARLAR') }}
            </div>

            <div class="overflow-hidden rounded-xl border border-line text-[12px] leading-snug sm:text-[13px]">
                @foreach ($project->technical_decisions ?? [] as $decision)
                    <div @class(['grid grid-cols-[110px_1fr] sm:grid-cols-[132px_1fr]', 'bg-row' => $loop->odd])>
                        <div class="px-3 py-2 font-extrabold text-muted">{{ $decision['label'] }}</div>
                        <div class="px-3 py-2 text-ink">{{ $decision['value'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-line bg-[var(--bg-card)] p-4 sm:p-5">
        <div class="mb-4 flex items-center justify-between gap-3 rounded-lg bg-accentSoft px-3 py-2 text-accentDark">
            <div class="flex items-center gap-2 text-[13px] font-black">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--bg-card)]">
                    <i data-lucide="badge-check" class="h-4 w-4"></i>
                </span>
                {{ __('ÖNE ÇIKAN ÖZELLİKLER') }}
            </div>
            <span class="hidden text-[11px] font-semibold text-muted sm:block">{{ __('MVP kapsamı') }}</span>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($project->features ?? [] as $feature)
                <article class="flex gap-3 rounded-xl border border-line bg-soft p-3.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[var(--bg-card)] text-accent">
                        <i data-lucide="{{ $feature['icon'] }}" class="h-4.5 w-4.5"></i>
                    </div>
                    <div>
                        <h2 class="text-[13px] font-black text-ink">{{ $feature['title'] }}</h2>
                        <p class="mt-1 text-[11.5px] leading-relaxed text-muted">{{ $feature['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ($project->metrics ?? [] as $metric)
            <div class="rounded-2xl border border-line bg-[var(--bg-card)] p-4 text-center">
                <i data-lucide="{{ $metric['icon'] }}" class="mx-auto h-4 w-4 text-accent"></i>
                <div class="mt-2 text-2xl font-black text-ink">{{ $metric['value'] }}</div>
                <div class="mt-1 text-[10px] font-bold uppercase tracking-wide text-muted">{{ $metric['label'] }}</div>
            </div>
        @endforeach
    </section>
</div>
