<div class="space-y-6">
    <div>
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:text class="mt-1 text-sm text-zinc-500">İçerik ve gizlilik durumuna genel bakış.</flux:text>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <flux:card>
            <div class="flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-300">
                    <flux:icon.folder class="size-5" />
                </span>
                <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $projectCount }}</span>
            </div>
            <flux:heading size="lg" class="mt-4">Portfolio Projesi</flux:heading>
            <flux:button size="sm" variant="ghost" class="mt-2" :href="route('portfolio-manager.index')" wire:navigate>
                Projeleri yönet
            </flux:button>
        </flux:card>

        <flux:card>
            <div class="flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-950/40 dark:text-violet-300">
                    <flux:icon.cpu-chip class="size-5" />
                </span>
                <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $technologyCount }}</span>
            </div>
            <flux:heading size="lg" class="mt-4">Teknoloji</flux:heading>
            <flux:button size="sm" variant="ghost" class="mt-2" :href="route('technology-manager.index')" wire:navigate>
                Kataloğu yönet
            </flux:button>
        </flux:card>

        <flux:card>
            <div class="flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-300">
                    <flux:icon.inbox class="size-5" />
                </span>
                <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $unreadMessageCount }}</span>
            </div>
            <flux:heading size="lg" class="mt-4">Okunmamış Mesaj</flux:heading>
            <flux:button size="sm" variant="ghost" class="mt-2" :href="route('contact-manager')" wire:navigate>
                Mesajları görüntüle
            </flux:button>
        </flux:card>
    </div>

    <flux:card>
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
                @php($selectedTheme = $themeColors[$webThemeColor] ?? $themeColors['green'])
                <span
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-white shadow-sm"
                    style="background: {{ $selectedTheme['light']['accent'] }}"
                >
                    <flux:icon.globe-alt class="size-6" />
                </span>
                <div>
                    <flux:heading size="lg">Web Tema Rengi</flux:heading>
                    <flux:text class="mt-1 max-w-2xl text-sm text-zinc-500">
                        Public web arayüzünde kullanılan vurgu rengini seçer. CV, portfolyo, iletişim ve menü vurguları bu palete göre güncellenir.
                    </flux:text>
                    <flux:badge color="zinc" class="mt-3">
                        Seçili: {{ $selectedTheme['label'] }}
                    </flux:badge>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-2 sm:grid-cols-6 lg:w-[360px]">
                @foreach ($themeColors as $key => $themeColor)
                    <button
                        type="button"
                        wire:click="selectWebThemeColor('{{ $key }}')"
                        title="{{ $themeColor['label'] }}"
                        aria-label="{{ $themeColor['label'] }} tema rengini seç"
                        @class([
                            'group relative flex h-12 items-center justify-center rounded-xl border transition hover:scale-[1.03] focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900',
                            'border-zinc-900 ring-2 ring-zinc-900 ring-offset-2 dark:border-white dark:ring-white dark:ring-offset-zinc-900' => $webThemeColor === $key,
                            'border-zinc-200 dark:border-zinc-700' => $webThemeColor !== $key,
                        ])
                        style="background: linear-gradient(135deg, {{ $themeColor['light']['accent'] }}, {{ $themeColor['light']['dark'] }}); --tw-ring-color: {{ $themeColor['light']['accent'] }};"
                    >
                        @if ($webThemeColor === $key)
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/95 text-zinc-900 shadow-sm">
                                <flux:icon.check class="size-4" />
                            </span>
                        @endif
                        <span class="sr-only">{{ $themeColor['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </flux:card>

    <div class="grid gap-4 lg:grid-cols-3">
    <flux:card>
        <div class="flex h-full flex-col gap-5 justify-between">
            <div class="flex items-start gap-4">
                <span @class([
                    'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                    'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' => $privacyHidden,
                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => ! $privacyHidden,
                ])>
                    <flux:icon :name="$privacyHidden ? 'lock-closed' : 'eye'" class="size-6" />
                </span>
                <div>
                    <flux:heading size="lg">Kişisel İletişim Verileri</flux:heading>
                    <flux:text class="mt-1 max-w-2xl text-sm text-zinc-500">
                        Bu anahtar yalnızca iletişim modülünde “kişisel veri” olarak işaretlenen telefon, e-posta veya sosyal medya bilgilerini etkiler.
                    </flux:text>
                    <flux:badge :color="$privacyHidden ? 'amber' : 'green'" class="mt-3">
                        {{ $privacyHidden ? 'Public sayfada maskeleniyor' : 'Public sayfada görünür' }}
                    </flux:badge>
                </div>
            </div>

            <flux:switch
                wire:model.live="privacyHidden"
                label="Kişisel verileri gizle"
                description="Değişiklik anında uygulanır."
            />
        </div>
    </flux:card>

    <flux:card>
        <div class="flex h-full flex-col gap-5 justify-between">
            <div class="flex items-start gap-4">
                <span @class([
                    'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                    'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' => $noindex,
                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => ! $noindex,
                ])>
                    <flux:icon :name="$noindex ? 'eye-slash' : 'globe-alt'" class="size-6" />
                </span>
                <div>
                    <flux:heading size="lg">Arama Motoru Görünürlüğü</flux:heading>
                    <flux:text class="mt-1 max-w-2xl text-sm text-zinc-500">
                        Sitenin genelini kapsayacak şekilde tüm sayfalarda "noindex, nofollow" meta etiketi kullanılmasını sağlar.
                    </flux:text>
                    <flux:badge :color="$noindex ? 'red' : 'green'" class="mt-3">
                        {{ $noindex ? 'Arama motorlarına kapalı' : 'Arama motorlarına açık' }}
                    </flux:badge>
                </div>
            </div>

            <flux:switch
                wire:model.live="noindex"
                label="Arama motorlarını engelle"
                description="Değişiklik anında uygulanır."
            />
        </div>
    </flux:card>

    <flux:card>
        <div class="flex h-full flex-col gap-5 justify-between">
            <div class="flex items-start gap-4">
                <span @class([
                    'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                    'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' => $blockVisitorsWithoutReferenceToken,
                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => ! $blockVisitorsWithoutReferenceToken,
                ])>
                    <flux:icon :name="$blockVisitorsWithoutReferenceToken ? 'lock-closed' : 'link'" class="size-6" />
                </span>
                <div>
                    <flux:heading size="lg">Referans Tokeni Zorunluluğu</flux:heading>
                    <flux:text class="mt-1 max-w-2xl text-sm text-zinc-500">
                        Bu anahtar açıkken public CV, hakkımda, portfolyo ve iletişim sayfaları yalnızca URL'de "rt" parametresi olan geçerli tokenlerle açılır.
                    </flux:text>
                    <flux:badge :color="$blockVisitorsWithoutReferenceToken ? 'amber' : 'green'" class="mt-3">
                        {{ $blockVisitorsWithoutReferenceToken ? 'Sadece tokenli ziyaretçiler kabul ediliyor' : 'Public erişim serbest' }}
                    </flux:badge>
                </div>
            </div>

            <flux:switch
                wire:model.live="blockVisitorsWithoutReferenceToken"
                label="Tokensiz ziyaretçileri engelle"
                description="Tokensiz ziyaretçiler bilgilendirme sayfasına yönlendirilir."
            />
        </div>
    </flux:card>
    </div>

    <flux:card>
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="lg">Referans Token Ziyaretleri</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500">
                    En çok ziyaret alan tokenler büyükten küçüğe sıralanır. Liste ekrana sığması için ilk 10 kayıtla sınırlandırılır.
                </flux:text>
            </div>
            <flux:button size="sm" variant="ghost" icon="link" :href="route('reference-token-manager.index')" wire:navigate>
                Tokenleri yönet
            </flux:button>
        </div>

        @if (count($referenceTokenChart) > 0)
            <div class="space-y-3">
                @foreach ($referenceTokenChart as $chartItem)
                    <div class="grid gap-2 lg:grid-cols-[minmax(180px,260px)_1fr_72px] lg:items-center">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-black text-zinc-900 dark:text-white">{{ $chartItem['name'] }}</div>
                            <div class="truncate font-mono text-[11px] font-bold text-zinc-400">{{ $chartItem['token'] }}</div>
                        </div>

                        <div class="h-9 overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                            <div
                                class="flex h-full min-w-2 items-center rounded-lg bg-emerald-500 px-3 text-xs font-black text-white transition-all dark:bg-emerald-400 dark:text-zinc-950"
                                style="width: {{ $chartItem['percentage'] }}%"
                            >
                                @if ($chartItem['percentage'] >= 18)
                                    {{ $chartItem['visits_count'] }}
                                @endif
                            </div>
                        </div>

                        <div class="text-right text-sm font-black text-zinc-900 dark:text-white">
                            {{ $chartItem['visits_count'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-zinc-200 px-5 py-10 text-center dark:border-zinc-700">
                <flux:icon.chart-column class="mx-auto size-9 text-zinc-300" />
                <flux:heading size="lg" class="mt-3">Henüz token ziyareti yok</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500">Ziyaret geldikçe en yüksek performanslı tokenler burada listelenir.</flux:text>
            </div>
        @endif
    </flux:card>
</div>
