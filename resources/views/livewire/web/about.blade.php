@php
    $isTurkish = app()->getLocale() === 'tr';
@endphp

<div
    class="about-page space-y-6 pb-4"
>
    <style>
        @property --about-angle {
            syntax: '<angle>';
            initial-value: 0deg;
            inherits: false;
        }

        .about-full-bleed {
            position: relative;
            width: 100%;
        }

        .about-panel-image {
            height: 100%;
            inset: 0;
            max-width: none;
            object-fit: cover;
            position: absolute;
            width: 100%;
            transform: none;
            transition: filter .5s ease;
        }

        .about-hero-grid {
            grid-template-columns: repeat(var(--panel-count), minmax(0, 1fr));
        }

        .about-grid {
            background-image:
                linear-gradient(var(--color-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--color-line) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: linear-gradient(to bottom, black, transparent);
            opacity: .34;
        }

        .about-grid-drift {
            animation: none;
        }

        .about-headline {
            animation: about-headline-shimmer 8s ease-in-out infinite;
            background: linear-gradient(100deg, var(--color-ink) 0 42%, var(--color-accent) 50%, var(--color-ink) 58% 100%);
            background-clip: text;
            background-size: 240% 100%;
            color: transparent;
            -webkit-background-clip: text;
        }

        .about-eyebrow-line {
            animation: about-line-pulse 2.8s ease-in-out infinite;
            transform-origin: left;
        }

        .about-reveal {
            animation: about-reveal .7s cubic-bezier(.2, .8, .2, 1) both;
            animation-delay: var(--reveal-delay, 0ms);
        }

        .about-orbit-border {
            isolation: isolate;
            position: relative;
        }

        .about-orbit-border::before {
            animation: about-border-spin var(--border-speed, 7s) linear infinite;
            background: conic-gradient(
                from var(--about-angle),
                transparent 0 24%,
                color-mix(in srgb, var(--color-accent) 82%, transparent) 34%,
                transparent 45% 72%,
                color-mix(in srgb, #6366f1 58%, transparent) 82%,
                transparent 92%
            );
            border-radius: inherit;
            content: '';
            inset: -1px;
            padding: 1px;
            pointer-events: none;
            position: absolute;
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            z-index: 3;
        }

        .about-reactive {
            --pointer-x: 50%;
            --pointer-y: 50%;
            --tilt-x: 0deg;
            --tilt-y: 0deg;
            isolation: isolate;
            position: relative;
            transform: none;
            transition: border-color .3s ease, box-shadow .3s ease;
        }



        .about-reactive-content {
            position: relative;
            transform: none;
            z-index: 1;
        }

        .about-loop-icon {
            animation: none;
        }

        .about-loop-icon .lucide {
            animation: none;
            transform-origin: center;
        }

        .about-hero-stage {
            perspective: 1200px;
            position: relative;
        }

        .about-hero-aurora {
            animation: about-aurora 10s ease-in-out infinite alternate;
            background:
                radial-gradient(circle at 24% 42%, rgba(45, 212, 191, .22), transparent 25%),
                radial-gradient(circle at 74% 54%, rgba(99, 102, 241, .18), transparent 29%);
            filter: blur(34px);
            inset: -20%;
            pointer-events: none;
            position: absolute;
        }



        .about-profile {
            animation: about-profile-enter .85s cubic-bezier(.2, .8, .2, 1) both;
            background: linear-gradient(145deg, color-mix(in srgb, var(--color-accent) 22%, var(--bg-card)), var(--bg-card));
            border: 3px solid var(--color-accent);
            border-radius: 9999px;
            box-shadow:
                0 0 0 7px color-mix(in srgb, var(--color-accent) 12%, transparent),
                0 18px 42px -18px color-mix(in srgb, var(--color-accent) 70%, transparent);
            height: 150px;
            overflow: hidden;
            position: relative;
            width: 150px;
        }

        .about-profile::after {
            animation: about-profile-scan 4.2s ease-in-out infinite;
            background: linear-gradient(90deg, transparent, rgba(153, 246, 228, .9), transparent);
            box-shadow: 0 0 16px rgba(45, 212, 191, .72);
            content: '';
            height: 1px;
            left: 8%;
            opacity: .8;
            position: absolute;
            right: 8%;
            top: 12%;
        }

        .about-profile img {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .about-panel-shell {
            animation: none;
            height: 100%;
            min-width: 0;
        }

        .about-panel {
            height: 100%;
            transform: none;
            transition: border-color .35s ease, box-shadow .35s ease;
        }



        .about-card-icon {
            position: relative;
        }

        .about-card-icon::after {
            animation: none;
            border: 1px solid color-mix(in srgb, var(--color-accent) 55%, transparent);
            border-radius: inherit;
            content: '';
            inset: 0;
            pointer-events: none;
            position: absolute;
        }

        .about-principle {
            overflow: hidden;
            position: relative;
        }

        .about-principle::before {
            background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--color-accent) 14%, transparent), transparent);
            content: '';
            inset: 0;
            position: absolute;
            transform: translateX(-110%);
            transition: transform .7s ease;
        }

        .about-principle:hover::before {
            transform: translateX(110%);
        }

        .about-quote {
            animation: about-quote-gradient 8s ease infinite;
            background:
                linear-gradient(120deg, var(--color-accentSoft), color-mix(in srgb, var(--color-accentSoft) 72%, #6366f1 28%), var(--color-accentSoft));
            background-size: 220% 220%;
        }

        @keyframes about-border-spin {
            to { --about-angle: 360deg; }
        }

        @keyframes about-grid-drift {
            to { background-position: 32px 32px; }
        }

        @keyframes about-headline-shimmer {
            0%, 72%, 100% { background-position: 100% 50%; }
            86% { background-position: 0 50%; }
        }

        @keyframes about-line-pulse {
            0%, 100% { opacity: .6; transform: scaleX(.65); }
            50% { opacity: 1; transform: scaleX(1); }
        }

        @keyframes about-reveal {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes about-icon-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes about-icon-turn {
            0%, 82%, 100% { transform: rotate(0deg) scale(1); }
            90% { transform: rotate(12deg) scale(1.08); }
        }

        @keyframes about-aurora {
            from { opacity: .55; transform: translate3d(-3%, -2%, 0) scale(.95); }
            to { opacity: 1; transform: translate3d(4%, 3%, 0) scale(1.08); }
        }



        @keyframes about-profile-enter {
            from { opacity: 0; transform: translateX(-24px) scale(.86); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes about-profile-scan {
            0%, 100% { opacity: 0; transform: translateY(0); }
            14%, 82% { opacity: .82; }
            90% { opacity: 0; transform: translateY(82px); }
        }

        @keyframes about-panel-enter {
            from {
                clip-path: inset(0 0 100% 0 round .75rem);
                filter: blur(7px);
                opacity: 0;
                transform: translateY(52px) scale(.96);
            }
            to {
                clip-path: inset(0 0 0 0 round .75rem);
                filter: blur(0);
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes about-panel-enter-left {
            from {
                clip-path: inset(0 100% 0 0 round .75rem);
                filter: blur(7px);
                opacity: 0;
                transform: translateX(-52px) scale(.96);
            }
            to {
                clip-path: inset(0 0 0 0 round .75rem);
                filter: blur(0);
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes about-panel-enter-right {
            from {
                clip-path: inset(0 0 0 100% round .75rem);
                filter: blur(7px);
                opacity: 0;
                transform: translateX(52px) scale(.96);
            }
            to {
                clip-path: inset(0 0 0 0 round .75rem);
                filter: blur(0);
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes about-ring-pulse {
            0% { opacity: .65; transform: scale(.85); }
            80%, 100% { opacity: 0; transform: scale(1.45); }
        }

        @keyframes about-quote-gradient {
            0%, 100% { background-position: 0 50%; }
            50% { background-position: 100% 50%; }
        }

        @media (prefers-reduced-motion: reduce) {
            .about-page *,
            .about-page *::before,
            .about-page *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
            }

            .about-reactive {
                transform: none !important;
            }
        }

        @media (max-width: 1279px) {
            .about-hero-grid {
                grid-template-columns: 1fr;
                height: auto;
            }

            .about-panel-shell {
                min-height: 260px;
            }
        }

        @media (max-width: 767px) {
            .about-panel-shell {
                min-height: 220px;
            }

            .about-focus-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <div class="flex justify-end gap-2">
        @foreach (['tr' => 'TR', 'en' => 'EN'] as $locale => $label)
            <button type="button" wire:click="setLocale('{{ $locale }}')" @class([
                'cursor-pointer rounded-md px-3 py-1 text-[12px] font-bold',
                'bg-accent text-white' => app()->getLocale() === $locale,
                'bg-[var(--bg-card)] text-muted hover:text-ink' => app()->getLocale() !== $locale,
            ])>{{ $label }}</button>
        @endforeach
    </div>

    @if ($privacyHidden && $profileIsPersonal)
        <div class="flex gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-amber-900 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-100">
            <i data-lucide="shield-alert" class="mt-0.5 h-5 w-5 shrink-0"></i>
            <p class="text-[12px] font-semibold leading-relaxed sm:text-[13px]">{{ $privacyNotice }}</p>
        </div>
    @endif

    <header
        class="about-reactive about-orbit-border relative overflow-hidden rounded-2xl border border-line bg-[var(--bg-card)] p-5 sm:p-8"
        style="--border-speed: 11s;"
    >
        <div class="about-grid about-grid-drift pointer-events-none absolute inset-0"></div>
        <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1fr)_220px] lg:items-end">
            <div class="grid items-center gap-6 sm:grid-cols-[150px_minmax(0,1fr)]">
                <div class="about-profile mx-auto sm:mx-0" aria-label="{{ $isTurkish ? 'Profil görseli' : 'Profile image' }}">
                    @if ($privacyHidden && $profileIsPersonal)
                        <div class="flex h-full w-full flex-col items-center justify-center bg-soft text-muted backdrop-blur-xl" title="{{ $isTurkish ? 'Gizlilik sebebiyle maskelenmiştir.' : 'Hidden for privacy.' }}">
                            <i data-lucide="user-x" class="mb-2 h-10 w-10 opacity-50"></i>
                        </div>
                    @elseif ($profileImagePath)
                        <img src="{{ asset($profileImagePath) }}" alt="{{ $isTurkish ? 'Profil fotoğrafı' : 'Profile photo' }}" />
                    @else
                        <div class="flex h-full w-full items-center justify-center text-xl font-black tracking-[0.16em] text-accentDark">
                            ACT
                        </div>
                    @endif
                </div>

                <div>
                    <div class="about-reveal flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.22em] text-accent">
                        <span class="about-eyebrow-line h-px w-8 bg-accent"></span>
                        {{ $about['eyebrow'] }}
                    </div>
                    <h1 class="about-headline about-reveal mt-5 max-w-3xl text-4xl font-black leading-[0.95] tracking-[-0.045em] sm:text-5xl lg:text-6xl" style="--reveal-delay: 90ms;">
                        {{ $about['headline'] }}
                    </h1>
                    <p class="about-reveal mt-5 max-w-2xl text-[14px] leading-7 text-muted sm:text-[15px]" style="--reveal-delay: 180ms;">
                        {{ $about['intro'] }}
                    </p>
                </div>
            </div>

            <div
                class="about-reactive about-orbit-border about-reveal rounded-xl border border-line bg-soft p-4"
                style="--border-speed: 5s; --reveal-delay: 260ms;"
            >
                <div class="about-reactive-content flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-muted">
                    <span>{{ $about['current_label'] }}</span>
                    <span class="flex items-center gap-1.5 text-accentDark">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-accent"></span>
                        {{ $about['current_status'] }}
                    </span>
                </div>
                <p class="about-reactive-content mt-3 text-[13px] font-black leading-relaxed text-ink">
                    {{ $about['current_text'] }}
                </p>
            </div>
        </div>
    </header>

    <section class="about-full-bleed about-hero-stage overflow-hidden border-y border-white/10 bg-slate-950 py-4 sm:py-6">
        <div class="about-hero-aurora"></div>


        <div
            class="about-hero-grid relative z-[1] mx-auto grid h-[390px] w-[calc(100%-2rem)] max-w-[1500px] gap-2 sm:h-[480px] sm:w-[calc(100%-3rem)] sm:gap-3"
            style="--panel-count: {{ max(count($about['hero_panels']), 1) }};"
        >
            @foreach ($about['hero_panels'] as $panel)
                @php
                    $panelImagePath = $panel['image_path'] ?? $heroImagePath;
                @endphp
                <div class="about-panel-shell">
                    <article
                        class="about-panel about-orbit-border relative overflow-hidden rounded-xl border border-white/15 bg-slate-900 shadow-2xl"
                        style="--border-speed: {{ 6 + $loop->index }}s;"
                    >
                        @if ($panelImagePath)
                            <img
                                class="about-panel-image"
                                src="{{ asset($panelImagePath) }}"
                                alt=""
                                aria-hidden="true"
                            />
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/15"></div>

                        <div class="absolute inset-x-0 bottom-0 z-[3] p-3 text-white sm:p-5">
                            <h2 class="text-[12px] font-black sm:text-lg">
                                {{ $panel['title'] }}
                            </h2>
                            @if (! empty($panel['description']))
                                <p class="mt-2 max-w-sm text-[11px] font-semibold leading-relaxed text-white/75 sm:text-[12px]">
                                    {{ $panel['description'] }}
                                </p>
                            @endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1.3fr)_minmax(280px,0.7fr)]">
        <article
            class="about-reactive about-orbit-border relative overflow-hidden rounded-2xl border border-line bg-[var(--bg-card)] p-5 sm:p-7"
            style="--border-speed: 9s;"
        >
            <div class="about-grid about-grid-drift pointer-events-none absolute inset-0"></div>
            <div class="absolute right-[-55px] top-[-55px] h-40 w-40 rounded-full bg-accentSoft blur-3xl"></div>
            <div class="about-reactive-content">
                <div class="about-card-icon about-loop-icon flex h-11 w-11 items-center justify-center rounded-xl bg-accentSoft text-accent">
                    <i data-lucide="scan-search" class="h-5 w-5"></i>
                </div>
                <h2 class="mt-5 text-2xl font-black tracking-tight text-ink">
                    {{ $about['philosophy_title'] }}
                </h2>
                <p class="mt-3 max-w-2xl text-[13px] leading-7 text-muted sm:text-[14px]">
                    {{ $about['philosophy_text'] }}
                </p>

                
                @php($focusCardColumns = min(max(count($about['focus_cards']), 1), 3))
                <div
                    class="about-focus-grid mt-6 grid gap-3"
                    style="grid-template-columns: repeat({{ $focusCardColumns }}, minmax(0, 1fr));"
                >
                    @foreach ($about['focus_cards'] as $item)
                        <div
                            class="about-reactive rounded-xl border border-line bg-soft px-3 py-2.5"
                        >
                            <div class="about-reactive-content flex items-center gap-3">
                                <span class="about-loop-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[var(--bg-card)] text-accent shadow-sm" style="--icon-delay: {{ $loop->index * 260 }}ms;">
                                    <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="truncate text-[12px] font-black leading-tight text-ink">{{ $item['title'] }}</div>
                                    <div class="mt-0.5 line-clamp-2 text-[11px] leading-snug text-muted">{{ $item['text'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <aside
            class="about-orbit-border relative overflow-hidden rounded-2xl border border-line bg-[var(--bg-card)] p-5 sm:p-6"
            style="--border-speed: 12s;"
            x-data="{
                selected: ['fast', 'quality'],
                toggle(key) {
                    if (this.selected.includes(key)) {
                        if (this.selected.length === 1) return;
                        this.selected = this.selected.filter((item) => item !== key);
                        return;
                    }

                    this.selected = [...this.selected.slice(-1), key];
                },
                isActive(key) {
                    return this.selected.includes(key);
                },
                apply() {
                    window.alert(this.selected.length > 2
                        ? @js(__('O sadece rüyalarda olur.'))
                        : @js(__('Aynı fikirde olduğumuza sevindim.')));
                }
            }"
            data-principle-switcher
        >
            <div class="about-grid about-grid-drift pointer-events-none absolute inset-0"></div>
            <div class="relative z-[1] flex min-h-[300px] items-center justify-center">
                <div class="w-full max-w-xs space-y-3">
                    <div class="text-center">
                        <h2 class="text-[18px] font-black leading-tight text-ink">
                            {{ __('Ürününüzü nasıl tercih edersiniz?') }}
                        </h2>
                        <p class="mx-auto mt-2 max-w-[260px] text-[11px] font-semibold leading-relaxed text-muted">
                            {{ __('Lütfen size en uygun iki seçeneği belirleyin; üçüncüsü kendiliğinden elenir.') }}
                        </p>
                    </div>

                    @foreach ([
                        ['key' => 'cheap', 'icon' => 'badge-dollar-sign', 'label' => __('Çok Ucuz')],
                        ['key' => 'fast', 'icon' => 'rocket', 'label' => __('Çok Hızlı')],
                        ['key' => 'quality', 'icon' => 'gem', 'label' => __('Çok Kaliteli')],
                    ] as $option)
                        <button
                            type="button"
                            class="group grid w-full grid-cols-[42px_1fr_56px] items-center overflow-hidden rounded-xl border p-1.5 text-left transition duration-300 ease-out"
                            x-bind:class="isActive('{{ $option['key'] }}')
                                ? 'border-accent/50 bg-accentSoft text-ink shadow-sm'
                                : 'border-line bg-soft text-muted hover:border-accent/30 hover:text-ink'"
                            x-on:click="toggle('{{ $option['key'] }}')"
                            x-bind:aria-pressed="isActive('{{ $option['key'] }}')"
                        >
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-lg transition duration-300 ease-out"
                                x-bind:class="isActive('{{ $option['key'] }}') ? 'bg-accent text-white' : 'bg-[var(--bg-card)] text-muted'"
                            >
                                <i
                                    data-lucide="{{ $option['icon'] }}"
                                    class="h-[18px] w-[18px] transition duration-300 ease-out"
                                    x-bind:class="isActive('{{ $option['key'] }}') ? 'scale-110 rotate-[-6deg]' : 'scale-100 rotate-0'"
                                ></i>
                            </span>

                            <span class="min-w-0 px-3 text-[13px] font-black">{{ $option['label'] }}</span>

                            <span
                                class="relative h-8 w-14 overflow-hidden rounded-full border transition duration-300 ease-out"
                                x-bind:class="isActive('{{ $option['key'] }}')
                                    ? 'border-accent bg-accent shadow-inner'
                                    : 'border-line bg-[var(--bg-card)] shadow-inner'"
                            >
                                <span
                                    class="absolute inset-0 origin-left bg-white/20 transition duration-300 ease-out"
                                    x-bind:class="isActive('{{ $option['key'] }}') ? 'scale-x-100 opacity-100' : 'scale-x-0 opacity-0'"
                                ></span>
                                <span
                                    class="absolute top-1 h-6 w-6 rounded-full bg-white shadow transition-all duration-300 ease-[cubic-bezier(.2,.8,.2,1)]"
                                    x-bind:class="isActive('{{ $option['key'] }}') ? 'translate-x-7 scale-100' : 'translate-x-1 scale-90'"
                                ></span>
                            </span>
                        </button>
                    @endforeach

                    <div class="grid grid-cols-3 gap-1.5 pt-1">
                        @foreach (['cheap', 'fast', 'quality'] as $option)
                            <span
                                class="h-1.5 rounded-full transition"
                                x-bind:class="isActive('{{ $option }}') ? 'bg-accent' : 'bg-line'"
                            ></span>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        class="mt-2 inline-flex h-10 w-full items-center justify-center rounded-xl bg-accent px-4 text-[12px] font-black text-white shadow-sm transition hover:bg-accentDark"
                        x-on:click="apply()"
                    >
                        {{ __('Uygula') }}
                    </button>
                </div>
            </div>
        </aside>
    </section>

    <section class="grid gap-4 md:grid-cols-[1fr_auto] md:items-center">
        <div
            class="about-quote about-reactive about-orbit-border rounded-2xl border border-line p-5 sm:p-6"
            style="--border-speed: 10s;"
        >
            <div class="about-reactive-content flex items-start gap-4">
                <span class="about-card-icon about-loop-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[var(--bg-card)] text-accent">
                    <i data-lucide="quote" class="h-5 w-5"></i>
                </span>
                <div>
                    <p class="max-w-3xl text-[15px] font-black leading-7 text-ink sm:text-lg">
                        {{ $about['quote'] }}
                    </p>
                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.16em] text-accentDark">{{ $about['quote_attribution'] }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 md:w-64">
            <a href="{{ \App\Support\ReferenceUrl::route('portfolio.index') }}" wire:navigate
                class="about-reactive about-orbit-border group flex min-h-28 flex-col justify-between rounded-2xl border border-line bg-[var(--bg-card)] p-4 hover:border-accent">
                <span class="about-reactive-content flex h-full flex-col justify-between">
                    <span class="about-loop-icon inline-flex">
                        <i data-lucide="briefcase-business" class="h-5 w-5 text-accent"></i>
                    </span>
                    <span class="flex items-center justify-between text-[12px] font-black text-ink">
                        {{ $about['portfolio_cta'] }}
                        <i data-lucide="arrow-up-right" class="h-3.5 w-3.5 transition group-hover:-translate-y-0.5 group-hover:translate-x-0.5"></i>
                    </span>
                </span>
            </a>
            <a href="{{ \App\Support\ReferenceUrl::route('contact') }}" wire:navigate
                class="about-reactive about-orbit-border group flex min-h-28 flex-col justify-between rounded-2xl border border-line bg-[var(--bg-card)] p-4 hover:border-accent">
                <span class="about-reactive-content flex h-full flex-col justify-between">
                    <span class="about-loop-icon inline-flex" style="--icon-delay: 420ms;">
                        <i data-lucide="message-circle-more" class="h-5 w-5 text-accent"></i>
                    </span>
                    <span class="flex items-center justify-between text-[12px] font-black text-ink">
                        {{ $about['contact_cta'] }}
                        <i data-lucide="arrow-up-right" class="h-3.5 w-3.5 transition group-hover:-translate-y-0.5 group-hover:translate-x-0.5"></i>
                    </span>
                </span>
            </a>
        </div>
    </section>
</div>
