@php($isTurkish = $activeLang === 'tr')

<form wire:submit="save" class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-700">
                <flux:icon.message-circle class="size-5 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl">İletişim Yönetimi</flux:heading>
                <flux:text class="text-sm text-zinc-500">İletişim bilgileri, harita, gizlilik ve form mesajlarını yönetin.</flux:text>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button type="button" size="sm" :variant="$isTurkish ? 'primary' : 'ghost'" wire:click="switchLang('tr')">Türkçe</flux:button>
            <flux:button type="button" size="sm" :variant="$isTurkish ? 'ghost' : 'primary'" wire:click="switchLang('en')">English</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Kaydet</flux:button>
        </div>
    </div>

    @if ($errors->any())
        <flux:callout variant="danger" icon="exclamation-triangle" heading="Formda düzeltilmesi gereken alanlar var.">
            {{ $errors->first() }}
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(320px,0.75fr)]">
        <div class="space-y-6">
            <flux:card>
                <div class="mb-4 flex items-center gap-2">
                    <flux:icon.document-text class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Sayfa İçeriği - {{ strtoupper($activeLang) }}</flux:heading>
                </div>

                <div class="space-y-4">
                    <flux:input :label="$isTurkish ? 'Sayfa Başlığı' : 'Page Title'" wire:model="translations.{{ $activeLang }}.title" />
                    <flux:textarea :label="$isTurkish ? 'Giriş Metni' : 'Introduction'" wire:model="translations.{{ $activeLang }}.intro" rows="3" />
                    <flux:input :label="$isTurkish ? 'Form Başlığı' : 'Form Title'" wire:model="translations.{{ $activeLang }}.form_title" />
                    <flux:textarea :label="$isTurkish ? 'Gizlilik Uyarısı' : 'Privacy Notice'" wire:model="translations.{{ $activeLang }}.privacy_notice" rows="3" />
                    <flux:input :label="$isTurkish ? 'Başarı Mesajı' : 'Success Message'" wire:model="translations.{{ $activeLang }}.success_message" />
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <flux:icon.users class="size-5 text-zinc-500" />
                        <flux:heading size="lg">İletişim Kanalları</flux:heading>
                        <flux:badge size="sm" color="zinc">{{ count($itemOrder) }}</flux:badge>
                    </div>
                    <flux:button type="button" size="sm" variant="filled" icon="plus" wire:click="addItem">Kanal Ekle</flux:button>
                </div>

                <div class="space-y-3">
                    @forelse ($itemOrder as $position => $itemKey)
                        @php($item = $items[$itemKey])
                        <div
                            class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700"
                            wire:key="contact-item-{{ $activeLang }}-{{ $itemKey }}"
                        >
                            <div class="grid gap-3 lg:grid-cols-[44px_minmax(140px,0.7fr)_minmax(180px,1fr)_minmax(180px,1fr)]">
                                <x-admin.contact-icon-select
                                    :value="$item['icon']"
                                    :lang="$activeLang"
                                    wire:model="items.{{ $itemKey }}.icon"
                                />
                                <flux:input size="sm" :label="$isTurkish ? 'Etiket' : 'Label'" wire:model="items.{{ $itemKey }}.label.{{ $activeLang }}" placeholder="Telefon" />
                                <flux:input size="sm" label="Değer" wire:model="items.{{ $itemKey }}.value" placeholder="+90 5xx xxx xx xx" />
                                <flux:input size="sm" label="Bağlantı" wire:model="items.{{ $itemKey }}.url" placeholder="tel:, mailto: veya https://" />
                            </div>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap gap-4">
                                    <flux:checkbox wire:model="items.{{ $itemKey }}.is_active" label="Yayında" />
                                    <flux:checkbox wire:model="items.{{ $itemKey }}.is_private" label="Kişisel veri" />
                                    <flux:checkbox wire:model="items.{{ $itemKey }}.show_in_cv" label="CV'de göster" />
                                </div>
                                <div class="flex gap-1">
                                    <flux:button type="button" size="xs" variant="ghost" icon="chevron-up" wire:click="moveItem('{{ $itemKey }}', -1)" :disabled="$position === 0" />
                                    <flux:button type="button" size="xs" variant="ghost" icon="chevron-down" wire:click="moveItem('{{ $itemKey }}', 1)" :disabled="$position === count($itemOrder) - 1" />
                                    <flux:button type="button" size="xs" variant="ghost" icon="trash" class="text-red-500" wire:click="removeItem('{{ $itemKey }}')" />
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-zinc-200 py-10 text-center dark:border-zinc-700">
                            <flux:text class="text-sm text-zinc-500">Henüz iletişim kanalı eklenmedi.</flux:text>
                        </div>
                    @endforelse
                </div>
            </flux:card>
        </div>

        <div class="space-y-6">
            <flux:card>
                <div class="mb-4 flex items-center gap-2">
                    <flux:icon.shield-check class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Gizlilik</flux:heading>
                </div>

                <div @class([
                    'rounded-xl border p-4',
                    'border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-950/30' => $privacy_hidden,
                    'border-zinc-200 dark:border-zinc-700' => ! $privacy_hidden,
                ])>
                    <flux:switch
                        wire:model.live="privacy_hidden"
                        label="Kişisel iletişim verilerini gizle"
                        description="Kişisel veri olarak işaretlenen değerler public sayfada maskelenir."
                    />
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-4 flex items-center gap-2">
                    <flux:icon.map-pin class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Konum ve Harita</flux:heading>
                </div>

                <div class="space-y-4">
                    <flux:input :label="$isTurkish ? 'Konum' : 'Location'" wire:model="translations.{{ $activeLang }}.location" placeholder="İzmir, Türkiye" />
                    <flux:textarea label="Google Maps Embed URL" wire:model="map_url" rows="4" placeholder="https://www.google.com/maps/embed?..." />
                    <flux:text class="text-xs text-zinc-400">Google Maps iframe kodunun yalnızca <code>src</code> adresini girin.</flux:text>
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <flux:icon.inbox class="size-5 text-zinc-500" />
                        <flux:heading size="lg">Gelen Mesajlar</flux:heading>
                    </div>
                    @if ($unreadCount)
                        <flux:badge color="blue">{{ $unreadCount }} okunmamış</flux:badge>
                    @endif
                </div>

                <div class="max-h-[620px] space-y-3 overflow-y-auto">
                    @forelse ($messages as $contactMessage)
                        <details @class([
                            'group rounded-xl border p-3',
                            'border-blue-300 bg-blue-50 dark:border-blue-800 dark:bg-blue-950/20' => ! $contactMessage->read_at,
                            'border-zinc-200 dark:border-zinc-700' => $contactMessage->read_at,
                        ]) wire:click="markAsRead({{ $contactMessage->id }})">
                            <summary class="cursor-pointer list-none">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $contactMessage->subject }}</div>
                                        <div class="mt-1 truncate text-xs text-zinc-500">{{ $contactMessage->name }} · {{ $contactMessage->email }}</div>
                                    </div>
                                    <span class="shrink-0 text-[10px] text-zinc-400">{{ $contactMessage->created_at->diffForHumans() }}</span>
                                </div>
                            </summary>
                            <p class="mt-3 whitespace-pre-line border-t border-zinc-200 pt-3 text-sm leading-relaxed text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">{{ $contactMessage->message }}</p>
                            <div class="mt-3 flex justify-end">
                                <flux:button type="button" size="xs" variant="ghost" icon="trash" class="text-red-500"
                                    wire:click.stop="deleteMessage({{ $contactMessage->id }})"
                                    wire:confirm="Bu mesajı silmek istediğinize emin misiniz?">Sil</flux:button>
                            </div>
                        </details>
                    @empty
                        <div class="py-8 text-center text-sm text-zinc-500">Henüz mesaj bulunmuyor.</div>
                    @endforelse
                </div>
            </flux:card>
        </div>
    </div>
</form>
