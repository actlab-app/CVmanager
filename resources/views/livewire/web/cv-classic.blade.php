@php
    $contacts = collect($cvData['contact_items'] ?? []);
    $skills = collect($cvData['skills'] ?? []);
    $quickInfos = collect($cvData['quick_infos'] ?? []);
    $isLocationInfo = fn($info): bool => data_get($info, 'icon') === 'map-pin'
        || in_array(mb_strtolower((string) data_get($info, 'title')), ['konum', 'location'], true);
    $locationInfo = $quickInfos->first($isLocationInfo);
    $displayQuickInfos = $quickInfos->reject($isLocationInfo);
    $experiences = collect($cvData['experiences'] ?? []);
    $educations = collect($cvData['educations'] ?? []);
    $projects = collect($classicData['projects'] ?? [])->take(8);
    $technologyCatalog = $classicData['technology_catalog'] ?? [];
    $portfolioUrl = data_get($cvData, 'qr_url');
    $portfolioDisplayUrl = \App\Support\ReferenceUrl::displayHost($portfolioUrl);
@endphp

<div>
    <style>
        .classic-cv {
            --classic-blue: var(--color-accentDark);
            --classic-blue-soft: var(--color-accentSoft);
            --classic-ink: #172033;
            --classic-muted: #526074;
            --classic-line: #d9e2ef;
            color: var(--classic-ink);
        }

        html.dark .classic-cv {
            --classic-ink: #e5eefb;
            --classic-muted: #a7b4c7;
            --classic-line: #26364d;
        }

        .classic-cv-section-title {
            color: var(--classic-blue);
            border-bottom: 1px solid var(--classic-line);
            letter-spacing: 0.08em;
        }

        @media screen {
            .classic-projects-page {
                margin-top: 1.5rem;
            }
        }

        @media print {

            html,
            body {
                height: auto !important;
                min-height: 297mm !important;
                margin: 0 !important;
                overflow: visible !important;
            }

            body>.container,
            .web-content-area,
            .web-grid {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }

            .classic-print-hidden {
                display: none !important;
            }

            .classic-cv {
                --classic-blue: var(--color-accentDark);
                --classic-blue-soft: var(--color-accentSoft);
                --classic-ink: #172033;
                --classic-muted: #526074;
                --classic-line: #d9e2ef;
            }

            main.classic-cv:first-of-type {}

            .classic-cv .classic-print-tight {}

            .classic-cv .classic-cv-section-title {
                margin-bottom: 5px !important;
                padding-bottom: 3px !important;
                font-size: 11px !important;
            }

            .classic-cv .classic-header {
                padding: 10px 12px !important;
                border-radius: 12px !important;
            }

            .classic-cv h1 {
                font-size: 24px !important;
            }

            .classic-cv h2,
            .classic-cv h3 {
                font-size: 11px !important;
            }

            /* Page 1 (First page) print body text sizes */
            main.classic-cv:first-of-type p,
            main.classic-cv:first-of-type li,
            main.classic-cv:first-of-type .classic-info-title,
            main.classic-cv:first-of-type .classic-info-value,
            main.classic-cv:first-of-type .classic-edu-degree,
            main.classic-cv:first-of-type .classic-edu-school,
            main.classic-cv:first-of-type .classic-skills-category,
            main.classic-cv:first-of-type .classic-skills-details {
                font-size: 12px !important;
                line-height: 1.35 !important;
            }

            .classic-cv .classic-header p {
                font-size: 11px !important;
            }

            .classic-cv .classic-chip,
            .classic-projects-page .classic-chip {
                padding: 2px 6px !important;
                font-size: 9px !important;
            }

            .classic-cv footer {
                font-size: 9px !important;
            }

            .classic-projects-page {
                margin-top: 0 !important;
                padding: 6mm 9mm !important;
            }

            .classic-projects-grid {
                grid-template-columns: 1fr !important;
                display: block !important;
            }

            .classic-project-item {
                margin-top: 2.5mm !important;
                padding-bottom: 0 !important;
            }

            .classic-projects-page .classic-cv-section-title {
                margin-bottom: 3mm !important;
            }

            .classic-projects-page h3 {
                font-size: 10px !important;
            }

            .classic-projects-page p {
                font-size: 10px !important;
                line-height: 1.35 !important;
                margin-top: 1mm !important;
            }

            .classic-project-summary {
                display: -webkit-box !important;
                -webkit-box-orient: vertical !important;
                -webkit-line-clamp: 2 !important;
                overflow: hidden !important;
            }
        }
    </style>

    <div class="classic-print-hidden mb-4 flex justify-end gap-2 print:hidden">
        @foreach (['tr' => 'TR', 'en' => 'EN'] as $locale => $label)
            <a href="{{ \App\Support\ReferenceUrl::route('cv', ['locale' => $locale]) }}" @class([
                'cursor-pointer rounded-md px-3 py-1 text-[12px] font-bold',
                'bg-accent text-white' => app()->getLocale() === $locale,
                'bg-[var(--bg-card)] text-muted hover:text-ink' => app()->getLocale() !== $locale,
            ])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <main class="a4-page classic-cv w-full rounded-2xl bg-[var(--bg-card)] p-4 shadow-xl sm:p-6 md:p-8 print:my-0">
        <header
            class="classic-header rounded-2xl border border-[var(--classic-line)] bg-[var(--classic-blue-soft)] p-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1
                        class="text-[30px] font-black leading-tight tracking-tight text-[var(--classic-ink)] sm:text-[36px]">
                        {{ $cvData['full_name'] }}
                    </h1>
                    <p class="mt-1 text-[14px] font-extrabold text-[var(--classic-blue)] sm:text-[16px]">
                        {{ $cvData['job_title'] }}
                    </p>
                </div>

                <div
                    class="flex flex-wrap gap-1.5 text-[11px] font-bold text-[var(--classic-muted)] justify-between w-full">
                    @foreach ($contacts as $contactItem)
                        @php
                            $contactUrl = data_get($contactItem, 'url');
                            $contactLabel = trim((string) data_get($contactItem, 'label'));
                        @endphp

                        @if ($contactUrl)
                            <a class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1 hover:text-[var(--classic-blue)]"
                                href="{{ $contactUrl }}"
                                target="{{ str_starts_with($contactUrl, 'http') ? '_blank' : '_self' }}"
                                rel="noopener noreferrer">
                                @if ($contactLabel !== '')
                                    <span class="text-[var(--classic-ink)]">{{ $contactLabel }}:</span>
                                @endif
                                {{ data_get($contactItem, 'value') }}
                            </a>
                        @else
                            <span
                                class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1">
                                @if ($contactLabel !== '')
                                    <span class="text-[var(--classic-ink)]">{{ $contactLabel }}:</span>
                                @endif
                                {{ data_get($contactItem, 'value') }}
                            </span>
                        @endif
                    @endforeach

                    @if ($locationInfo)
                        <span
                            class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1">
                            <span
                                class="text-[var(--classic-ink)]">{{ app()->getLocale() === 'en' ? 'Location' : (data_get($locationInfo, 'title') ?: 'Konum') }}:</span>
                            {{ data_get($locationInfo, 'value') }}
                        </span>
                    @endif

                    @if ($portfolioUrl)
                        <a class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1 hover:text-[var(--classic-blue)]"
                            href="{{ $portfolioUrl }}" target="_blank" rel="noopener noreferrer">
                            {{ __('Portfolyo') }}: {{ $portfolioDisplayUrl }}
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <section class="classic-print-tight mt-5 pt-3">
            <h2 class="classic-cv-section-title mb-2 pb-1 text-[12px] font-black uppercase">
                {{ __('Profesyonel Özet') }}
            </h2>
            <p class="text-[12.5px] leading-relaxed text-[var(--classic-muted)]">
                {{ $classicData['profile_summary'] }}
            </p>
        </section>

        <section class="classic-print-tight mt-5 pt-3 grid gap-5 md:grid-cols-[1fr_1fr]">
            <div>
                <h2 class="classic-cv-section-title mb-2 pb-1 text-[12px] font-black uppercase">
                    {{ __('Ek Bilgiler') }}
                </h2>
                <div class="classic-info-grid grid gap-1.5 text-[12px]">
                    @foreach ($displayQuickInfos->take(4) as $info)
                        <div class="grid grid-cols-[86px_1fr] gap-2">
                            <span
                                class="classic-info-title font-black text-[var(--classic-ink)]">{{ data_get($info, 'title') }}</span>
                            <span
                                class="classic-info-value text-[var(--classic-muted)]">{{ data_get($info, 'value') }}</span>
                        </div>
                    @endforeach
                </div>


            </div>
            <div>
                <h2 class="classic-cv-section-title mb-2 pb-1 text-[12px] font-black uppercase">
                    {{ __('Eğitim') }}
                </h2>
                <div class="classic-education-list space-y-1.5 text-[12px] text-[var(--classic-muted)]">
                    @foreach ($educations as $education)
                        <div>
                            <span
                                class="classic-edu-degree font-black text-[var(--classic-ink)]">{{ data_get($education, 'degree') }}</span>
                            <span class="classic-edu-school"> · {{ data_get($education, 'school') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>



        <section class="classic-print-tight mt-5 pt-3">
            <h2 class="classic-cv-section-title mb-2 pb-1 text-[12px] font-black uppercase">
                {{ __('Profesyonel Deneyim') }}
            </h2>

            <div class="space-y-3">
                @foreach ($experiences as $experience)
                    <article class="mb-5">
                        <h3 class="text-[13px] font-black text-[var(--classic-ink)]">
                            {{ data_get($experience, 'company') }}
                        </h3>
                        <p class="mt-0.5 text-[12px] font-extrabold text-[var(--classic-muted)]">
                            {{ data_get($experience, 'description') }}
                        </p>

                        @if (trim((string) data_get($experience, 'detailed_description', '')) !== '')
                            <p class="text-[12px] leading-relaxed text-[var(--classic-muted)]">
                                {!!  nl2br(data_get($experience, 'detailed_description')) !!}
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="classic-print-tight mt-5 pt-3">
            <h2 class="classic-cv-section-title mb-2 pb-1 text-[12px] font-black uppercase">
                {{ __('Teknik Yetkinlikler') }}
            </h2>
            <div class="classic-skills-grid grid gap-x-4 gap-y-1.5 text-[12px]">
                @foreach ($skills as $skill)
                    <div class="grid grid-cols-[96px_1fr] gap-2">
                        <div class="classic-skills-category font-black text-[var(--classic-ink)]">
                            {{ data_get($skill, 'category') }}
                        </div>
                        <div class="classic-skills-details text-[var(--classic-muted)]">{{ data_get($skill, 'details') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <footer
            class="classic-print-tight mt-5 border-t border-[var(--classic-line)] pt-3 text-[10.5px] leading-relaxed text-[var(--classic-muted)]">
            {{ __('Belge Sonu') }}
        </footer>
    </main>

    @if ($projects->isNotEmpty())
        <main
            class="a4-page classic-cv classic-projects-page w-full rounded-2xl bg-[var(--bg-card)] p-4 shadow-xl sm:p-6 md:p-8 print:my-0">
            <header
                class="classic-header mb-5 rounded-2xl border border-[var(--classic-line)] bg-[var(--classic-blue-soft)] p-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div
                        class="flex flex-wrap gap-1.5 text-[11px] font-bold text-[var(--classic-muted)] justify-between w-full">
                        @foreach ($contacts as $contactItem)
                            @php
                                $contactUrl = data_get($contactItem, 'url');
                                $contactLabel = trim((string) data_get($contactItem, 'label'));
                            @endphp

                            @if ($contactUrl)
                                <a class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1 hover:text-[var(--classic-blue)]"
                                    href="{{ $contactUrl }}"
                                    target="{{ str_starts_with($contactUrl, 'http') ? '_blank' : '_self' }}"
                                    rel="noopener noreferrer">
                                    @if ($contactLabel !== '')
                                        <span class="text-[var(--classic-ink)]">{{ $contactLabel }}:</span>
                                    @endif
                                    {{ data_get($contactItem, 'value') }}
                                </a>
                            @else
                                <span
                                    class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1">
                                    @if ($contactLabel !== '')
                                        <span class="text-[var(--classic-ink)]">{{ $contactLabel }}:</span>
                                    @endif
                                    {{ data_get($contactItem, 'value') }}
                                </span>
                            @endif
                        @endforeach

                        @if ($locationInfo)
                            <span
                                class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1">
                                <span
                                    class="text-[var(--classic-ink)]">{{ app()->getLocale() === 'en' ? 'Location' : (data_get($locationInfo, 'title') ?: 'Konum') }}:</span>
                                {{ data_get($locationInfo, 'value') }}
                            </span>
                        @endif

                        @if ($portfolioUrl)
                            <a class="classic-chip rounded-full border border-[var(--classic-line)] bg-[var(--bg-card)] px-2.5 py-1 hover:text-[var(--classic-blue)]"
                                href="{{ $portfolioUrl }}" target="_blank" rel="noopener noreferrer">
                                {{ __('Portfolyo') }}: {{ $portfolioDisplayUrl }}
                            </a>
                        @endif
                    </div>
                </div>
            </header>

            <section>
                <h2 class="classic-cv-section-title mb-3 pb-1 text-[12px] font-black uppercase">
                    {{ __('Seçilmiş Projeler') }}
                </h2>

                <div class="classic-projects-grid grid gap-3 mt-5 md:grid-cols-2">
                    @foreach ($projects as $projectIndex => $project)
                        @php
                            $projectTechs = collect(data_get($project, 'technologies', []))
                                ->map(fn($slug) => data_get($technologyCatalog, $slug . '.name'))
                                ->filter()
                                ->take(7)
                                ->implode(', ');
                            $metrics = collect(data_get($project, 'metrics', []))->take(4);
                            $projectUrl = data_get($project, 'url');
                        @endphp

                        <article @class([
                            'classic-project-item',
                            'mt-5 border-t border-[var(--classic-line)] pt-5' => $projectIndex > 0,
                        ])>
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                                <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                                    <h3 class="text-[13px] font-black text-[var(--classic-ink)]">
                                        {{ data_get($project, 'title') }}
                                    </h3>
                                    @if ($projectUrl)
                                        <a class="text-[10.5px] font-black text-[var(--classic-blue)] underline-offset-2 hover:underline"
                                            href="{{ $projectUrl }}" target="_blank" rel="noopener noreferrer">
                                            [{{ __('Portfolyo') }} <i data-lucide="arrow-up-right-from-square"
                                                class="w-3 h-3 inline"></i>]
                                        </a>
                                    @endif
                                </div>

                            </div>
                            <p class="classic-project-summary mt-1 text-[12px] leading-relaxed text-[var(--classic-muted)]">
                                {{ data_get($project, 'summary') }}
                            </p>

                            @if ($metrics->isNotEmpty())
                                <div class="mt-1.5 flex flex-wrap gap-1.5">
                                    @foreach ($metrics as $metric)
                                        <span
                                            class="classic-chip rounded-md bg-[var(--classic-blue-soft)] px-2 py-0.5 text-[10px] font-black text-[var(--classic-blue)]">
                                            [{{ data_get($metric, 'value') }}] {{ data_get($metric, 'label') }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($projectTechs)
                                <p class="mt-1 text-[10.5px] font-semibold text-[var(--classic-muted)]">
                                    {{ __('Teknolojiler') }}: {{ $projectTechs }}
                                </p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    @endif

    <div class="classic-print-hidden print-btn mt-4 flex justify-end print:hidden">
        <button type="button" onclick="window.print()"
            class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-accentDark/10 bg-accent px-5 py-2.5 font-bold text-white shadow-md transition-all duration-300 hover:scale-[1.02] hover:bg-accentDark active:scale-95">
            <i data-lucide="printer" class="h-3.5 w-3.5 text-white"></i>
            <span>{{ __('Yazdır') }}</span>
        </button>
    </div>
</div>
