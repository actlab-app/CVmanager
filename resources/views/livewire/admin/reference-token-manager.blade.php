@php
    use App\Support\ReferenceUrl;
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-700">
                <flux:icon.link class="size-5 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl">Referans Tokenleri</flux:heading>
                <flux:text class="text-sm text-zinc-500">Başvuru kaynakları için ölçümlenebilir public linkler oluşturun.</flux:text>
            </div>
        </div>

        @if ($referenceTokenId)
            <flux:button type="button" size="sm" variant="ghost" wire:click="create">Yeni token oluştur</flux:button>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(340px,0.8fr)_minmax(0,1.2fr)]">
        <form wire:submit="save" class="space-y-6">
            <flux:card>
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <flux:heading size="lg">{{ $referenceTokenId ? 'Tokeni Düzenle' : 'Yeni Token' }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500">Token değeri URL'de "rt" parametresi olarak taşınır.</flux:text>
                    </div>
                    <flux:badge :color="$is_active ? 'green' : 'zinc'">
                        {{ $is_active ? 'Aktif' : 'Pasif' }}
                    </flux:badge>
                </div>

                <div class="space-y-4">
                    <flux:input label="Kaynak Adı" wire:model="name" placeholder="Microsoft" icon="building-office" />

                    <div class="grid grid-cols-[1fr_auto] gap-2">
                        <flux:input label="Token" wire:model="token" placeholder="LJ5O45I6J86J" icon="key" />
                        <div class="flex items-end">
                            <flux:button type="button" variant="ghost" icon="arrow-path" wire:click="generateToken">
                                Üret
                            </flux:button>
                        </div>
                    </div>

                    <flux:textarea label="Not" wire:model="description" rows="3" placeholder="Bu token Microsoft başvurusu için kullanılacak." />

                    <div>
                        <flux:label>Baloncuk Görseli</flux:label>
                        <label class="mt-2 flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-zinc-200 p-3 transition hover:border-accent dark:border-zinc-700">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                @if ($image)
                                    <img class="h-full w-full object-cover" src="{{ $image->temporaryUrl() }}" alt="" />
                                @elseif ($existingImagePath && ! $removeExistingImage)
                                    <img class="h-full w-full object-cover" src="{{ asset($existingImagePath) }}" alt="" />
                                @else
                                    <flux:icon.photo class="size-6 text-zinc-400" />
                                @endif
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Görsel seç</span>
                                <span class="block text-xs text-zinc-400">PNG, JPG veya WebP · maksimum 4 MB</span>
                            </span>
                            <input type="file" class="sr-only" wire:model="image" accept="image/jpeg,image/png,image/webp" />
                        </label>

                        @if ($image || ($existingImagePath && ! $removeExistingImage))
                            <flux:button type="button" size="xs" variant="ghost" icon="trash" class="mt-2 text-red-500" wire:click="removeImage">
                                Görseli kaldır
                            </flux:button>
                        @endif
                        <flux:error name="image" />
                    </div>

                    <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                        <flux:checkbox wire:model="is_active" label="Token aktif" description="Pasif tokenler public girişte geçersiz kabul edilir." />
                    </div>

                    <flux:button type="submit" variant="primary" icon="check" class="w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $referenceTokenId ? 'Değişiklikleri Kaydet' : 'Tokeni Oluştur' }}</span>
                        <span wire:loading wire:target="save">Kaydediliyor...</span>
                    </flux:button>
                </div>
            </flux:card>
        </form>

        <div class="space-y-4">
            @forelse ($tokens as $referenceToken)
                <flux:card wire:key="reference-token-{{ $referenceToken->id }}">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex min-w-0 flex-1 gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                                @if ($referenceToken->image)
                                    <img class="h-full w-full object-cover" src="{{ asset($referenceToken->image) }}" alt="" />
                                @else
                                    <flux:icon.link class="size-6" />
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:heading size="lg">{{ $referenceToken->name }}</flux:heading>
                                    <flux:badge :color="$referenceToken->is_active ? 'green' : 'zinc'">
                                        {{ $referenceToken->is_active ? 'Aktif' : 'Pasif' }}
                                    </flux:badge>
                                    <span class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-1 text-[11px] font-black uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                        <flux:icon.eye class="size-3.5" />
                                        {{ $referenceToken->visits_count }} ziyaret
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-1 text-[11px] font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                        <flux:icon.clock class="size-3.5" />
                                        {{ $referenceToken->last_visited_at?->format('d.m.Y H:i') ?? '-' }}
                                    </span>
                                </div>
                                <div class="mt-1 font-mono text-sm font-black text-zinc-900 dark:text-white">{{ $referenceToken->token }}</div>
                                @if ($referenceToken->description)
                                    <flux:text class="mt-2 text-sm text-zinc-500">{{ $referenceToken->description }}</flux:text>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 sm:flex sm:items-center">
                            <flux:button type="button" size="sm" variant="ghost" icon="chart-column" wire:click="showDetail({{ $referenceToken->id }})">
                                Detay
                            </flux:button>
                            <flux:button type="button" size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $referenceToken->id }})">
                                Düzenle
                            </flux:button>
                            <flux:button type="button" size="sm" variant="danger" icon="trash" wire:click="delete({{ $referenceToken->id }})" wire:confirm="Bu referans tokeni ve ziyaret geçmişi silinsin mi?">
                                Sil
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @empty
                <flux:card>
                    <div class="py-12 text-center">
                        <flux:icon.link class="mx-auto size-10 text-zinc-300" />
                        <flux:heading size="lg" class="mt-3">Henüz referans tokeni yok</flux:heading>
                        <flux:text class="mt-1 text-sm text-zinc-500">İlk tokeni oluşturarak kaynak bazlı ziyaret ölçümüne başlayın.</flux:text>
                    </div>
                </flux:card>
            @endforelse
        </div>
    </div>

    <flux:modal
        name="reference-token-detail-modal"
        wire:model="showDetailModal"
        @close="closeDetail"
        class="w-[96vw] max-w-7xl"
    >
        @if ($detailToken)
            <div class="max-h-[82vh] space-y-5 overflow-y-auto pr-1">
                <div class="flex flex-col gap-4 border-b border-zinc-200 pb-5 dark:border-zinc-700 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex min-w-0 gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                            @if ($detailToken->image)
                                <img class="h-full w-full object-cover" src="{{ asset($detailToken->image) }}" alt="" />
                            @else
                                <flux:icon.link class="size-6" />
                            @endif
                        </span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:heading size="xl">{{ $detailToken->name }}</flux:heading>
                                <flux:badge :color="$detailToken->is_active ? 'green' : 'zinc'">
                                    {{ $detailToken->is_active ? 'Aktif' : 'Pasif' }}
                                </flux:badge>
                            </div>
                            <div class="mt-1 font-mono text-sm font-black text-zinc-900 dark:text-white">{{ $detailToken->token }}</div>
                            @if ($detailToken->description)
                                <flux:text class="mt-2 max-w-4xl text-sm text-zinc-500">{{ $detailToken->description }}</flux:text>
                            @endif
                        </div>
                    </div>

                    <flux:modal.close>
                        <flux:button type="button" size="sm" variant="ghost" icon="x-mark">
                            Kapat
                        </flux:button>
                    </flux:modal.close>
                </div>

                <details open class="group rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-black text-zinc-700 marker:hidden dark:text-zinc-200">
                        <span class="inline-flex min-w-0 items-center gap-2">
                            <flux:icon.chart-column class="size-4 shrink-0" />
                            <span>Ziyaret Analitiği</span>
                            <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                {{ $detailStats['total_visits'] }} ziyaret
                            </span>
                        </span>
                        <flux:icon.chevron-down class="size-4 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>

                    <div class="space-y-5 border-t border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                            <flux:input type="date" label="Başlangıç" wire:model.live="detailDateFrom" />
                            <flux:input type="date" label="Bitiş" wire:model.live="detailDateTo" />
                            <flux:button type="button" variant="ghost" icon="x-mark" wire:click="resetDetailDateRange">
                                Temizle
                            </flux:button>
                        </div>

                        <div class="grid gap-4 xl:grid-cols-2">
                            @foreach ([
                                ['title' => 'IP Dağılımı', 'chart' => $detailStats['ip_chart'], 'summary' => $detailStats['ip_chart']['groups_count'].' farklı IP'],
                                ['title' => 'User Agent Dağılımı', 'chart' => $detailStats['user_agent_chart'], 'summary' => $detailStats['user_agent_chart']['groups_count'].' farklı user agent'],
                            ] as $pie)
                                <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <div>
                                            <div class="text-sm font-black text-zinc-900 dark:text-white">{{ $pie['title'] }}</div>
                                            <div class="text-xs font-semibold text-zinc-500">{{ $pie['summary'] }}</div>
                                        </div>
                                        <flux:icon.chart-column class="size-5 text-zinc-400" />
                                    </div>

                                    @if (count($pie['chart']['items']) > 0)
                                        <div class="grid gap-4 md:grid-cols-[10rem_minmax(0,1fr)] md:items-center">
                                            <div class="mx-auto size-40 rounded-full shadow-inner ring-1 ring-zinc-200 dark:ring-zinc-700" style="background: {{ $pie['chart']['gradient'] }}"></div>
                                            <div class="space-y-2">
                                                @foreach ($pie['chart']['items'] as $item)
                                                    <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-2 text-xs">
                                                        <span class="size-3 rounded-sm" style="background: {{ $item['color'] }}"></span>
                                                        <span class="min-w-0 truncate font-bold text-zinc-600 dark:text-zinc-300">{{ $item['label'] }}</span>
                                                        <span class="font-black text-zinc-900 dark:text-white">{{ $item['count'] }} · {{ $item['percentage'] }}%</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="rounded-xl border border-dashed border-zinc-200 px-4 py-8 text-center dark:border-zinc-700">
                                            <flux:icon.chart-column class="mx-auto size-8 text-zinc-300" />
                                            <flux:text class="mt-2 text-sm text-zinc-500">Bu tarih aralığında veri yok.</flux:text>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-black text-zinc-900 dark:text-white">Ziyaret Edilen Sayfalar</div>
                                    <div class="text-xs font-semibold text-zinc-500">{{ $detailStats['page_chart']['total'] }} sayfa ziyareti</div>
                                </div>
                                <flux:icon.chart-column class="size-5 text-zinc-400" />
                            </div>

                            @if (count($detailStats['page_chart']['items']) > 0)
                                <div class="space-y-3">
                                    @foreach ($detailStats['page_chart']['items'] as $item)
                                        <div class="grid gap-2 lg:grid-cols-[minmax(120px,220px)_1fr_64px] lg:items-center">
                                            <div class="min-w-0 truncate text-sm font-black text-zinc-700 dark:text-zinc-200">{{ $item['label'] }}</div>
                                            <div class="h-8 overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                                <div class="flex h-full min-w-2 items-center rounded-lg bg-blue-500 px-3 text-xs font-black text-white dark:bg-blue-400 dark:text-zinc-950" style="width: {{ $item['percentage'] }}%">
                                                    @if ($item['percentage'] >= 18)
                                                        {{ $item['count'] }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right text-sm font-black text-zinc-900 dark:text-white">{{ $item['count'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-xl border border-dashed border-zinc-200 px-4 py-8 text-center dark:border-zinc-700">
                                    <flux:icon.chart-column class="mx-auto size-8 text-zinc-300" />
                                    <flux:text class="mt-2 text-sm text-zinc-500">Bu tarih aralığında sayfa ziyareti yok.</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>
                </details>

                <details class="group rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-black text-zinc-700 marker:hidden dark:text-zinc-200">
                        <span class="inline-flex min-w-0 items-center gap-2">
                            <flux:icon.link class="size-4 shrink-0" />
                            <span>Önizleme Linkleri</span>
                        </span>
                        <flux:icon.chevron-down class="size-4 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>

                    <div class="space-y-2 border-t border-zinc-200 p-4 dark:border-zinc-700">
                        @foreach (['cv' => 'CV', 'portfolio.index' => 'Portfolyo', 'contact' => 'İletişim'] as $routeName => $label)
                            @php($link = ReferenceUrl::appendToken(route($routeName), $detailToken->token))
                            <div class="grid grid-cols-[6rem_minmax(0,1fr)_auto] items-center gap-3 rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-800">
                                <span class="text-xs font-black text-zinc-500">{{ $label }}</span>
                                <span class="min-w-0 truncate font-mono text-xs text-zinc-600 dark:text-zinc-300">{{ $link }}</span>
                                <button type="button" class="rounded-md p-1.5 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-700 dark:hover:text-white" title="Kopyala" onclick="navigator.clipboard?.writeText(@js($link))">
                                    <flux:icon.clipboard class="size-4" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </details>

                <details class="group rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-black text-zinc-700 marker:hidden dark:text-zinc-200">
                        <span class="inline-flex min-w-0 items-center gap-2">
                            <flux:icon.users class="size-4 shrink-0" />
                            <span>Ziyaretçi Temizleme</span>
                            <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                {{ $detailStats['visitor_cleanup']['total'] }} ziyaretçi
                            </span>
                        </span>
                        <flux:icon.chevron-down class="size-4 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>

                    <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                        @if (count($detailStats['visitor_cleanup']['items']) > 0)
                            <div class="space-y-2">
                                @foreach ($detailStats['visitor_cleanup']['items'] as $visitor)
                                    <div class="grid gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800/60 lg:grid-cols-[minmax(0,1fr)_8rem_11rem_auto] lg:items-center">
                                        <div class="grid gap-2 md:grid-cols-2">
                                            <div class="min-w-0">
                                                <div class="text-[11px] font-black uppercase tracking-wide text-zinc-400">IP Hash</div>
                                                <div class="mt-1 truncate font-mono text-xs font-bold text-zinc-700 dark:text-zinc-200" title="{{ $visitor['ip_hash'] ?? 'Yok' }}">
                                                    {{ $visitor['ip_hash_short'] }}
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-[11px] font-black uppercase tracking-wide text-zinc-400">User Agent Hash</div>
                                                <div class="mt-1 truncate font-mono text-xs font-bold text-zinc-700 dark:text-zinc-200" title="{{ $visitor['user_agent_hash'] ?? 'Yok' }}">
                                                    {{ $visitor['user_agent_hash_short'] }}
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="text-[11px] font-black uppercase tracking-wide text-zinc-400">Toplam</div>
                                            <div class="mt-1 text-sm font-black text-zinc-900 dark:text-white">{{ $visitor['visits_count'] }} ziyaret</div>
                                        </div>

                                        <div>
                                            <div class="text-[11px] font-black uppercase tracking-wide text-zinc-400">Son Ziyaret</div>
                                            <div class="mt-1 text-sm font-bold text-zinc-700 dark:text-zinc-200">{{ $visitor['last_visited_at'] }}</div>
                                        </div>

                                        <div class="flex justify-end">
                                            <flux:button
                                                type="button"
                                                size="sm"
                                                variant="danger"
                                                icon="trash"
                                                wire:click="confirmVisitorVisitDeletion(@js($visitor['ip_hash']), @js($visitor['user_agent_hash']))"
                                            >
                                                Sil
                                            </flux:button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-zinc-200 px-4 py-8 text-center dark:border-zinc-700">
                                <flux:icon.users class="mx-auto size-8 text-zinc-300" />
                                <flux:text class="mt-2 text-sm text-zinc-500">Temizlenecek ziyaretçi kaydı yok.</flux:text>
                            </div>
                        @endif
                    </div>
                </details>
            </div>
        @endif
    </flux:modal>

    <flux:modal
        name="reference-token-visitor-deletion-modal"
        wire:model="showVisitorDeletionModal"
        @close="closeVisitorDeletionModal"
        class="max-w-lg"
    >
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">Ziyaretçiyi temizle</flux:heading>
                <flux:text class="mt-2 text-sm text-zinc-500">
                    Bu ziyaretçinin bu tokene yaptığı tüm ziyaretleri temizlemek istiyor musunuz?
                </flux:text>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 text-xs dark:border-zinc-700 dark:bg-zinc-800">
                <div class="font-mono text-zinc-600 dark:text-zinc-300">IP: {{ $pendingVisitorIpHash ?? 'Yok' }}</div>
                <div class="mt-1 font-mono text-zinc-600 dark:text-zinc-300">UA: {{ $pendingVisitorUserAgentHash ?? 'Yok' }}</div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">
                        Vazgeç
                    </flux:button>
                </flux:modal.close>

                <flux:button type="button" variant="danger" icon="trash" wire:click="deleteConfirmedVisitorVisits">
                    Evet, temizle
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
