<div>
    <style>
        @media print {
            .cv-project-type-card {
                min-height: 36px !important;
                padding: 4px 5px !important;
                align-items: center !important;
            }

            .cv-project-type-icon {
                align-self: center !important;
                grid-row: 1 / span 2 !important;
                height: 20px !important;
                width: 20px !important;
            }

            .cv-project-type-card {
                grid-template-columns: 20px minmax(0, 1fr) !important;
                grid-template-rows: auto auto !important;
                column-gap: 4px !important;
                row-gap: 1px !important;
            }

            .cv-project-type-title {
                font-size: 9px !important;
                grid-column: 2 !important;
                margin: 0 !important;
            }

            .cv-project-type-description {
                font-size: 7.5px !important;
                grid-column: 2 !important;
                line-height: 1.15 !important;
                margin: 0 !important;
            }
        }
    </style>

    <div class="mb-4 flex justify-end gap-2 print:hidden">
        @foreach (['tr' => 'TR', 'en' => 'EN'] as $locale => $label)
            <a href="{{ route('cv', ['locale' => $locale]) }}" @class([
                'cursor-pointer rounded-md px-3 py-1 text-[12px] font-bold',
                'bg-accent text-white' => app()->getLocale() === $locale,
                'bg-[var(--bg-card)] text-muted hover:text-ink' => app()->getLocale() !== $locale,
            ])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <main class="a4-page w-full rounded-2xl bg-[var(--bg-card)] p-4 shadow-xl sm:p-6 md:p-8 print:my-0">
        <header
            @class([
                'grid grid-cols-1 gap-5 rounded-2xl border border-line bg-soft p-2 sm:p-2',
                'lg:grid-cols-[250px_1fr]' => $cvData['qr_url'],
            ])>
            @if ($cvData['qr_url'])
            <aside
                class="flex w-full flex-col items-center justify-center rounded-2xl border border-accent/30 bg-[var(--bg-card)] p-3 shadow-sm md:max-w-none"
                aria-label="Portfolyo QR kodu">


                <img
                    class="h-32 w-32 object-contain sm:h-36 sm:w-36"
                    src="{{ asset('images/portfolio-qr.png') }}"
                    alt="Detaylı portfolyo QR kodu"
                />

                <div class="mt-2 flex items-center gap-1.5 rounded-lg bg-accentSoft px-2.5 py-1.5 text-center text-[11px] font-black leading-tight text-accentDark">
                    <i data-lucide="globe-2" class="h-3.5 w-3.5"></i>
                    <span>Website & Portfolyo test</span>
                </div>

                <p class="mt-1 max-w-[180px] text-center text-[11px] leading-snug text-muted">
                    QR kodu okutabilir veya bağlantıyı açabilirsiniz.
                </p>

                <a
                    href="{{ $cvData['qr_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="cv-qr-link mt-2 inline-flex items-center gap-1.5 rounded-full bg-accent px-3 py-1.5 text-[11px] font-black text-white transition hover:bg-accentDark"
                >
                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                    Bağlantıya git
                </a>

            </aside>
            @endif

            <section class="flex flex-col justify-between py-1">
                <div>
                    <h1
                        class="text-[28px] font-black leading-none tracking-tight text-ink sm:text-[36px] md:text-[42px]">
                        {{ $cvData['full_name'] }}
                    </h1>
                    <p class="mt-2 mb-3 text-[15px] font-extrabold text-accentDark sm:text-[18px]">
                        {{ $cvData['job_title'] }}
                    </p>
                </div>

                <div>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($cvData['contact_items'] as $contactItem)
                        @php($contactUrl = data_get($contactItem, 'url'))

                        @if ($contactUrl)
                            <a href="{{ $contactUrl }}"
                                target="{{ str_starts_with($contactUrl, 'http') ? '_blank' : '_self' }}"
                                rel="noopener noreferrer"
                                class="group flex min-h-[62px] flex-col justify-center rounded-xl border border-line bg-[var(--bg-card)] p-2.5 text-ink transition hover:border-accent hover:text-accentDark">
                                <div class="flex min-w-0 items-center gap-1.5">
                                    <span
                                        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-accentSoft text-accentDark">
                                        <x-contact-icon :name="data_get($contactItem, 'icon', 'link')"
                                            class="h-3.5 w-3.5" />
                                    </span>
                                    <span class="min-w-0 truncate text-[11px] font-black text-muted">
                                        {{ data_get($contactItem, 'label') }}
                                    </span>
                                </div>
                                <div class="mt-1 truncate text-[12px] font-extrabold leading-tight">
                                    {{ data_get($contactItem, 'value') }}
                                </div>
                            </a>
                        @else
                            <div
                                class="flex min-h-[62px] flex-col justify-center rounded-xl border border-line bg-[var(--bg-card)] p-2.5 text-ink">
                                <div class="flex min-w-0 items-center gap-1.5">
                                    <span
                                        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-accentSoft text-accentDark">
                                        <x-contact-icon :name="data_get($contactItem, 'icon', 'link')"
                                            class="h-3.5 w-3.5" />
                                    </span>
                                    <span class="min-w-0 truncate text-[11px] font-black text-muted">
                                        {{ data_get($contactItem, 'label') }}
                                    </span>
                                </div>
                                <div class="mt-1 truncate text-[12px] font-extrabold leading-tight">
                                    {{ data_get($contactItem, 'value') }}
                                </div>
                            </div>
                        @endif
                        @endforeach
                    </div>
                    <div
                        class="mt-1 rounded-xl border border-line bg-[var(--bg-card)] p-3 text-[13px] leading-relaxed text-ink sm:p-4 sm:text-[14px]">
                        {!! $cvData['about_content'] !!}
                    </div>
                </div>
            </section>
        </header>

        <section class="mt-4 grid grid-cols-1 gap-4 sm:mt-6 sm:gap-5 xl:grid-cols-2">
            <div class="space-y-4">


                <x-web.cv-section :title="__('HIZLI BİLGİ')" icon="list-checks" :items="$cvData['quick_infos']"
                    label-key="title" value-key="value" compact />
            </div>

            <div class="space-y-2">
                <x-web.cv-section :title="__('EĞİTİM BİLGİLERİ')" icon="route" :items="$cvData['educations']"
                    label-key="degree" value-key="school" compact />
                <x-web.cv-section :title="__('PROFESYONEL DENEYİM')" icon="briefcase" :items="$cvData['experiences']"
                    label-key="company" value-key="description" compact />
            </div>
        </section>

        <section class="mt-4 rounded-2xl bg-[var(--bg-card)] sm:mt-6">
            <div
                class="mb-3 flex items-center gap-2 rounded-lg bg-accentSoft px-3 py-2 text-[12px] font-black tracking-wide text-accentDark sm:mb-4 sm:px-4 sm:text-[13px]">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--bg-card)] text-[17px]">
                    <i data-lucide="folder-kanban"></i>
                </span>
                {{ __('PROJE TİPLERİ') }}
            </div>

            <div class="grid grid-cols-4 gap-2">
                @foreach ($cvData['project_types'] as $projectType)
                    <div
                        class="cv-project-type-card grid min-h-[70px] grid-cols-[28px_minmax(0,1fr)] grid-rows-[auto_auto] items-center gap-x-1 gap-y-1 rounded-xl border border-line bg-row px-2.5 py-2 text-ink">
                        <span
                            class="cv-project-type-icon row-span-2 flex h-7 w-7 shrink-0 items-center justify-center self-center rounded-lg bg-[var(--bg-card)] text-accentDark">
                            <i data-lucide="{{ data_get($projectType, 'icon', 'folder') }}" class="h-4 w-4"></i>
                        </span>
                        <div class="cv-project-type-title min-w-0 truncate text-[11px] font-black leading-tight">
                            {{ data_get($projectType, 'type') }}
                        </div>
                        <p class="cv-project-type-description min-w-0 line-clamp-2 text-[9.5px] font-medium leading-snug text-muted">
                            {{ data_get($projectType, 'description') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-4 sm:mt-6">
            <x-web.cv-section :title="__('TEKNİK YETKİNLİK')" icon="layers-3" :items="$cvData['skills']"
                label-key="category" value-key="details" compact />
        </section>

        <footer
            class="mt-4 flex flex-col items-start justify-between gap-2 border-t border-line pt-3 text-[11px] leading-relaxed text-muted sm:mt-6 sm:flex-row sm:items-center sm:gap-0 sm:pt-4 sm:text-[12px]">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-check" class="text-[15px]"></i>
                <span>
                    {{ __('Bu döküman kişisel veriler içermektedir. Havuzlarda saklanmak için üretilmemiştir inceleme sonrası silinmesi önemle rica olunur.') }}
                </span>
            </div>
            <div class="flex shrink-0 items-center gap-2 whitespace-nowrap font-semibold">
                <i data-lucide="mail" class="text-[15px]"></i>
                ahmetct35@gmail.com
            </div>
        </footer>
    </main>

    <div class="print-btn mt-4 flex justify-end print:hidden">
        <button type="button" onclick="window.print()"
            class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-accentDark/10 bg-accent px-5 py-2.5 font-bold text-white shadow-md transition-all duration-300 hover:scale-[1.02] hover:bg-accentDark active:scale-95">
            <i data-lucide="printer" class="h-3.5 w-3.5 text-white"></i>
            <span>{{ __('Yazdır') }}</span>
        </button>
    </div>
</div>
