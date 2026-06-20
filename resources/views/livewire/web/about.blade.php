@php
    $isTurkish = app()->getLocale() === 'tr';
    $heroMotions = [
        ['offset' => 50, 'rotate' => -1.4, 'enter' => 'left'],
        ['offset' => -60, 'rotate' => 0, 'enter' => 'bottom'],
        ['offset' => 50, 'rotate' => 1.2, 'enter' => 'right'],
    ];
@endphp

<div
    class="about-page space-y-6 pb-4"
    x-data="{
        reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
        react(event, strength = 5) {
            if (this.reducedMotion) return;

            const element = event.currentTarget;
            const rect = element.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;

            element.style.setProperty('--pointer-x', `${(x / rect.width) * 100}%`);
            element.style.setProperty('--pointer-y', `${(y / rect.height) * 100}%`);
            element.style.setProperty('--tilt-x', `${(0.5 - y / rect.height) * strength}deg`);
            element.style.setProperty('--tilt-y', `${(x / rect.width - 0.5) * strength}deg`);
        },
        reset(event) {
            const element = event.currentTarget;
            element.style.setProperty('--pointer-x', '50%');
            element.style.setProperty('--pointer-y', '50%');
            element.style.setProperty('--tilt-x', '0deg');
            element.style.setProperty('--tilt-y', '0deg');
        }
    }"
>
    <style>
        @property --about-angle {
            syntax: '<angle>';
            initial-value: 0deg;
            inherits: false;
        }

        .about-full-bleed {
            left: 50%;
            margin-left: -50vw;
            position: relative;
            width: 100vw;
        }

        .about-panel-image {
            height: 116%;
            max-width: none;
            position: absolute;
            top: -8%;
            transform: translate3d(0, var(--image-y, 0), 0) scale(var(--image-scale, 1.08));
            transition: filter .5s ease, transform .2s ease-out;
            will-change: transform;
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
            animation: about-grid-drift 16s linear infinite;
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
            transform: perspective(900px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y));
            transform-style: preserve-3d;
            transition: border-color .3s ease, box-shadow .3s ease, transform .2s ease-out;
            will-change: transform;
        }



        .about-reactive-content {
            position: relative;
            transform: translateZ(18px);
            z-index: 1;
        }

        .about-loop-icon {
            animation: about-icon-float 3.8s ease-in-out infinite;
            animation-delay: var(--icon-delay, 0ms);
        }

        .about-loop-icon .lucide {
            animation: about-icon-turn 8s ease-in-out infinite;
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
            animation: about-panel-enter .9s cubic-bezier(.16, 1, .3, 1) both;
            animation-delay: var(--panel-delay, 0ms);
            height: 100%;
            min-width: 0;
        }

        .about-panel-shell--left {
            animation-name: about-panel-enter-left;
            animation-duration: 1s;
        }

        .about-panel-shell--right {
            animation-name: about-panel-enter-right;
            animation-duration: 1s;
        }

        .about-panel {
            height: 100%;
            transform:
                perspective(1000px)
                translate3d(0, var(--scroll-y, 0px), 0)
                scale(var(--panel-scale, 1))
                rotateX(var(--panel-rx, 0deg))
                rotateZ(var(--panel-rz, 0deg));
            transform-style: preserve-3d;
            transition: border-color .35s ease, box-shadow .35s ease, transform .18s ease-out;
            will-change: transform;
        }



        .about-card-icon {
            position: relative;
        }

        .about-card-icon::after {
            animation: about-ring-pulse 2.6s ease-out infinite;
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

            .about-motion,
            .about-reactive {
                transform: none !important;
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
        x-on:pointermove="react($event, 2.2)"
        x-on:pointerleave="reset($event)"
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
                x-on:pointermove="react($event, 6)"
                x-on:pointerleave="reset($event)"
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

    <section
        x-data="{
            progress: 0,
            update() {
                const rect = this.$refs.hero.getBoundingClientRect();
                this.progress = Math.max(0, Math.min(1, (window.innerHeight - rect.top) / (window.innerHeight + rect.height)));
            }
        }"
        x-init="
            update();
            const handleScroll = () => window.requestAnimationFrame(() => update());
            window.addEventListener('scroll', handleScroll, { passive: true });
            $cleanup(() => window.removeEventListener('scroll', handleScroll));
        "
        x-ref="hero"
        class="about-full-bleed about-hero-stage overflow-hidden border-y border-white/10 bg-slate-950 py-4 sm:py-6"
    >
        <div class="about-hero-aurora"></div>


        <div
            class="relative z-[1] mx-auto grid h-[390px] w-[calc(100%-2rem)] max-w-[1500px] gap-2 sm:h-[480px] sm:w-[calc(100%-3rem)] sm:gap-3"
            style="grid-template-columns: repeat({{ max(count($about['hero_panels']), 1) }}, minmax(0, 1fr));"
        >
            @foreach ($about['hero_panels'] as $panel)
                @php($motion = $heroMotions[$loop->index % count($heroMotions)])
                <div class="about-panel-shell about-panel-shell--{{ $motion['enter'] }}" style="--panel-delay: {{ 180 + ($loop->index * 150) }}ms;">
                    <article
                        class="about-panel about-motion about-orbit-border relative overflow-hidden rounded-xl border border-white/15 bg-slate-900 shadow-2xl"
                        x-bind:style="`
                            --panel-rz: {{ $motion['rotate'] }}deg;
                            --border-speed: {{ 6 + $loop->index }}s;
                            --scroll-y: ${(progress - .5) * {{ $motion['offset'] }}}px;
                            --panel-rx: ${(progress - .5) * {{ 3.2 + ($loop->index * .7) }}}deg;
                            --panel-scale: ${.975 + (Math.sin(progress * Math.PI) * .025)};
                            --image-y: ${(progress - .5) * {{ $motion['offset'] * -.75 }}}px;
                            --image-scale: ${1.1 - (Math.sin(progress * Math.PI) * .025)};
                        `"
                    >
                        @if ($heroImagePath)
                            <img
                                class="about-panel-image"
                                style="left: -{{ $loop->index * 100 }}%; width: {{ max(count($about['hero_panels']), 1) * 100 }}%;"
                                src="{{ asset($heroImagePath) }}"
                                alt=""
                                aria-hidden="true"
                            />
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/15"></div>

                        <div class="absolute inset-x-0 bottom-0 z-[3] p-3 text-white sm:p-5">
                            <div class="text-[10px] font-black tracking-[0.2em] text-accentDark">{{ $panel['number'] }}</div>
                            <h2 class="mt-1 text-[12px] font-black sm:text-lg">
                                {{ $panel['title'] }}
                            </h2>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)]">
        <article
            class="about-reactive about-orbit-border relative overflow-hidden rounded-2xl border border-line bg-[var(--bg-card)] p-5 sm:p-7"
            style="--border-speed: 9s;"
            x-on:pointermove="react($event, 2.8)"
            x-on:pointerleave="reset($event)"
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

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    @foreach ($about['focus_cards'] as $item)
                        <div
                            class="about-reactive rounded-xl border border-line bg-soft p-3.5"
                            x-on:pointermove="react($event, 8)"
                            x-on:pointerleave="reset($event)"
                        >
                            <div class="about-reactive-content">
                                <span class="about-loop-icon inline-flex" style="--icon-delay: {{ $loop->index * 260 }}ms;">
                                    <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4 text-accent"></i>
                                </span>
                                <div class="mt-3 text-[12px] font-black text-ink">{{ $item['title'] }}</div>
                                <div class="mt-1 text-[11px] leading-relaxed text-muted">{{ $item['text'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <aside
            class="about-reactive about-orbit-border relative overflow-hidden rounded-2xl border border-line bg-[var(--bg-card)] p-5 sm:p-6"
            style="--border-speed: 12s;"
            x-on:pointermove="react($event, 3.5)"
            x-on:pointerleave="reset($event)"
        >
            <div class="about-grid about-grid-drift pointer-events-none absolute inset-0"></div>
            <div class="about-reactive-content flex items-center justify-between">
                <div class="text-[11px] font-black uppercase tracking-[0.18em] text-accentDark">
                    {{ $about['principles_title'] }}
                </div>
                <span class="about-loop-icon inline-flex">
                    <i data-lucide="fingerprint" class="h-5 w-5 text-accent"></i>
                </span>
            </div>

            <div class="about-reactive-content mt-5 space-y-2">
                @foreach ($about['principles'] as $principle)
                    <div class="about-principle group flex items-center gap-3 rounded-xl border border-transparent px-2 py-3 transition hover:border-line hover:bg-soft">
                        <span class="relative z-[1] text-[10px] font-black text-accent">{{ $principle['number'] }}</span>
                        <span class="h-px w-5 bg-line transition group-hover:w-8 group-hover:bg-accent"></span>
                        <span class="relative z-[1] text-[12px] font-bold text-ink sm:text-[13px]">{{ $principle['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </aside>
    </section>

    <section class="grid gap-4 md:grid-cols-[1fr_auto] md:items-center">
        <div
            class="about-quote about-reactive about-orbit-border rounded-2xl border border-line p-5 sm:p-6"
            style="--border-speed: 10s;"
            x-on:pointermove="react($event, 2.5)"
            x-on:pointerleave="reset($event)"
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
                class="about-reactive about-orbit-border group flex min-h-28 flex-col justify-between rounded-2xl border border-line bg-[var(--bg-card)] p-4 hover:border-accent"
                x-on:pointermove="react($event, 9)"
                x-on:pointerleave="reset($event)">
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
                class="about-reactive about-orbit-border group flex min-h-28 flex-col justify-between rounded-2xl border border-line bg-[var(--bg-card)] p-4 hover:border-accent"
                x-on:pointermove="react($event, 9)"
                x-on:pointerleave="reset($event)">
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
