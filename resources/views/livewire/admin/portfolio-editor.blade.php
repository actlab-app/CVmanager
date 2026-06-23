@php($isTurkish = $activeLang === 'tr')
@php($iconCatalog = config('cv-icons'))

<form wire:submit="save" class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <flux:button
                type="button"
                variant="ghost"
                icon="arrow-left"
                :href="route('portfolio-manager.index')"
                wire:navigate
            />
            <div>
                <flux:heading size="xl">{{ $projectId ? 'Projeyi Düzenle' : 'Yeni Proje' }}</flux:heading>
                <flux:text class="text-sm text-zinc-500">Portfolio detay sayfasının tüm içeriğini buradan yönetin.</flux:text>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button type="button" size="sm" :variant="$isTurkish ? 'primary' : 'ghost'" wire:click="switchLang('tr')">
                🇹🇷 Türkçe
            </flux:button>
            <flux:button type="button" size="sm" :variant="$isTurkish ? 'ghost' : 'primary'" wire:click="switchLang('en')">
                🇬🇧 English
            </flux:button>
            <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Kaydet</span>
                <span wire:loading wire:target="save">Kaydediliyor...</span>
            </flux:button>
        </div>
    </div>

    @if ($errors->any())
        <flux:callout variant="danger" icon="exclamation-triangle" heading="Formda düzeltilmesi gereken alanlar var.">
            {{ $errors->first() }}
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,0.7fr)]">
        <div class="space-y-6">
            <flux:card>
                <div class="mb-4 flex items-center gap-2">
                    <flux:icon.document-text class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Proje İçeriği - {{ strtoupper($activeLang) }}</flux:heading>
                </div>

                <div class="space-y-4">
                    <flux:input
                        :label="$isTurkish ? 'Proje Adı' : 'Project Name'"
                        wire:model="translations.{{ $activeLang }}.title"
                        :placeholder="$isTurkish ? 'Örn. CV Manager' : 'e.g. CV Manager'"
                    />
                    <flux:textarea
                        :label="$isTurkish ? 'Kısa Açıklama' : 'Short Description'"
                        wire:model="translations.{{ $activeLang }}.short_description"
                        rows="3"
                    />
                    <flux:textarea
                        :label="$isTurkish ? 'Detaylı Açıklama' : 'Detailed Description'"
                        wire:model="translations.{{ $activeLang }}.detailed_description"
                        rows="5"
                    />

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <flux:input
                            :label="$isTurkish ? 'Proje Türü' : 'Project Type'"
                            wire:model="translations.{{ $activeLang }}.project_type"
                        />
                        <flux:input
                            :label="$isTurkish ? 'Rol' : 'Role'"
                            wire:model="translations.{{ $activeLang }}.role"
                        />
                        <flux:input
                            :label="$isTurkish ? 'Süre' : 'Duration'"
                            wire:model="translations.{{ $activeLang }}.duration"
                        />
                        <flux:input
                            :label="$isTurkish ? 'Platform' : 'Platform'"
                            wire:model="translations.{{ $activeLang }}.platform"
                        />
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <flux:icon.photo class="size-5 text-zinc-500" />
                        <flux:heading size="lg">Proje Görselleri</flux:heading>
                        <flux:badge size="sm" color="zinc">{{ count($existingImages) + count($uploads) }}/{{ $maxImages }}</flux:badge>
                        <flux:badge size="sm" color="amber" wire:loading wire:target="uploads">Yükleniyor...</flux:badge>
                    </div>
                    <flux:text class="text-xs text-zinc-400">JPG, PNG veya WebP - maksimum 5 MB</flux:text>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($existingImageOrder as $position => $imageKey)
                        @php($image = $existingImages[$imageKey])
                        <div
                            class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700"
                            wire:key="existing-image-{{ $activeLang }}-{{ $imageKey }}"
                        >
                            <img class="aspect-video w-full object-cover" src="{{ asset($image['path']) }}" alt="" />
                            <div class="space-y-2 p-3">
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Görsel başlığı' : 'Image title'"
                                    wire:model="existingImages.{{ $imageKey }}.translations.{{ $activeLang }}.title"
                                />
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Kısa açıklama' : 'Short description'"
                                    wire:model="existingImages.{{ $imageKey }}.translations.{{ $activeLang }}.description"
                                />
                                <div class="flex justify-end gap-1">
                                    <flux:button
                                        type="button"
                                        size="xs"
                                        variant="ghost"
                                        icon="chevron-left"
                                        wire:click="moveExistingImage({{ $position }}, -1)"
                                        :disabled="$position === 0"
                                    />
                                    <flux:button
                                        type="button"
                                        size="xs"
                                        variant="ghost"
                                        icon="chevron-right"
                                        wire:click="moveExistingImage({{ $position }}, 1)"
                                        :disabled="$position === count($existingImageOrder) - 1"
                                    />
                                    <flux:button
                                        type="button"
                                        size="xs"
                                        variant="ghost"
                                        icon="trash"
                                        class="text-red-500"
                                        wire:click="removeExistingImage({{ $position }})"
                                    />
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @foreach ($uploadOrder as $position => $uploadKey)
                        @php($upload = $uploads[$position])
                        <div
                            class="overflow-hidden rounded-xl border border-dashed border-accent"
                            wire:key="upload-{{ $activeLang }}-{{ $uploadKey }}"
                        >
                            <img class="aspect-video w-full object-cover" src="{{ $upload->temporaryUrl() }}" alt="" />
                            <div class="space-y-2 p-3">
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Görsel başlığı' : 'Image title'"
                                    wire:model="uploadTranslations.{{ $uploadKey }}.{{ $activeLang }}.title"
                                />
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Kısa açıklama' : 'Short description'"
                                    wire:model="uploadTranslations.{{ $uploadKey }}.{{ $activeLang }}.description"
                                />
                                <flux:button
                                    type="button"
                                    size="xs"
                                    variant="ghost"
                                    icon="trash"
                                    class="w-full text-red-500"
                                    wire:click="discardUpload({{ $position }})"
                                >
                                    Kaldır
                                </flux:button>
                            </div>
                        </div>
                    @endforeach

                    @if (count($existingImages) + count($uploads) < $maxImages)
                        <label
                            class="flex min-h-48 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-zinc-200 p-5 text-center transition hover:border-accent dark:border-zinc-700"
                            wire:loading.class="pointer-events-none opacity-60"
                            wire:target="uploads"
                        >
                            <flux:icon.cloud-arrow-up class="size-8 text-zinc-300 dark:text-zinc-600" />
                            <span class="mt-2 text-sm font-semibold text-zinc-600 dark:text-zinc-300" wire:loading.remove wire:target="uploads">Görsel seç</span>
                            <span class="mt-2 text-sm font-semibold text-zinc-600 dark:text-zinc-300" wire:loading wire:target="uploads">Görsel yükleniyor...</span>
                            <span class="mt-1 text-xs text-zinc-400" wire:loading.remove wire:target="uploads">Birden fazla dosya seçebilirsiniz</span>
                            <input
                                type="file"
                                class="sr-only"
                                wire:model="uploads"
                                wire:loading.attr="disabled"
                                wire:target="uploads"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                            />
                        </label>
                    @endif
                </div>
            </flux:card>
        </div>

        <div class="space-y-6">
            <flux:card>
                <div class="mb-4 flex items-center gap-2">
                    <flux:icon.cog-6-tooth class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Yayın Ayarları</flux:heading>
                </div>

                <div class="space-y-4">
                    <flux:input label="Slug" wire:model="slug" placeholder="cv-manager" />
                    <flux:select label="Durum" wire:model="status">
                        <flux:select.option value="draft">Taslak</flux:select.option>
                        <flux:select.option value="active">Aktif Geliştirme</flux:select.option>
                        <flux:select.option value="completed">Tamamlandı</flux:select.option>
                        <flux:select.option value="archived">Arşivlendi</flux:select.option>
                    </flux:select>
                    <flux:input type="date" label="Proje Tarihi" wire:model="project_date" />
                    <flux:input type="number" min="0" label="Sıralama" wire:model="sort_order" />
                    <flux:input label="Canlı Proje URL" wire:model="live_url" placeholder="https://..." />
                    <flux:input label="Repository URL" wire:model="repository_url" placeholder="https://github.com/..." />
                    <div class="space-y-3 rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                        <flux:checkbox wire:model="is_published" label="Yayında göster" />
                        <flux:checkbox wire:model="is_featured" label="Öne çıkan proje" />
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-4 flex items-center gap-2">
                    <flux:icon.cpu-chip class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Teknoloji Kataloğu</flux:heading>
                </div>

                <div class="grid max-h-[500px] grid-cols-2 gap-2 overflow-y-auto pr-1">
                    @foreach ($technologyCatalog as $technology)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-zinc-200 p-2.5 dark:border-zinc-700">
                            <input
                                type="checkbox"
                                value="{{ $technology['slug'] }}"
                                wire:model="technologySlugs"
                                class="rounded border-zinc-300 text-accent focus:ring-accent"
                            />
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                @if ($technology['logo_path'])
                                    <img
                                        class="h-4 w-4 object-contain"
                                        src="{{ asset($technology['logo_path']) }}"
                                        alt=""
                                    />
                                @else
                                    <flux:icon :name="$technology['render_icon']" variant="micro" class="text-zinc-500" />
                                @endif
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-xs font-bold text-zinc-700 dark:text-zinc-200">
                                    {{ $technology['name'] }}
                                </span>
                                <span class="block text-[10px] text-zinc-400">{{ $technology['category'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </flux:card>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <flux:card>
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.sparkles class="size-5 text-zinc-500" />
                    <flux:heading size="lg">{{ $isTurkish ? 'Öne Çıkan Özellikler' : 'Highlighted Features' }}</flux:heading>
                    <flux:badge size="sm" color="zinc">{{ count($features[$activeLang]) }}</flux:badge>
                </div>
                <flux:button size="sm" variant="primary" icon="plus" wire:click="addItem('features')">
                    Ekle
                </flux:button>
            </div>

            <div class="space-y-3">
                @forelse ($repeaterOrder['features'] as $position => $rowKey)
                    @php($item = $features[$activeLang][$rowKey])
                    @php($selectedIcon = $iconCatalog[$item['icon'] ?? ''] ?? null)
                    <div
                        class="flex items-start gap-2 rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800"
                        wire:key="features-{{ $activeLang }}-{{ $rowKey }}"
                    >
                        <div class="flex min-w-0 flex-1 items-center gap-2">
                            <flux:select
                                variant="listbox"
                                searchable
                                size="sm"
                                placeholder="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}"
                                empty="{{ $isTurkish ? 'Eşleşen ikon bulunamadı.' : 'No matching icon found.' }}"
                                class="w-10! shrink-0"
                                wire:model.live="features.{{ $activeLang }}.{{ $rowKey }}.icon"
                            >
                                <x-slot name="button">
                                    <flux:select.button
                                        size="sm"
                                        class="w-10! justify-center px-2! [&>svg:last-child]:hidden"
                                        title="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}"
                                        aria-label="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}"
                                    >
                                        <flux:select.selected placeholder="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}" class="justify-center" />
                                    </flux:select.button>
                                </x-slot>

                                <x-slot name="search">
                                    <flux:select.search placeholder="{{ $isTurkish ? 'İkon ara...' : 'Search icons...' }}" />
                                </x-slot>

                                <flux:select.option value="">
                                    <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                                        <flux:icon.x-mark variant="micro" class="text-zinc-400" />
                                        <span class="flex-1 [ui-selected_&]:hidden">{{ $isTurkish ? 'İkon yok' : 'No icon' }}</span>
                                    </div>
                                </flux:select.option>

                                @if (($item['icon'] ?? '') !== '' && ! $selectedIcon)
                                    <flux:select.option :value="$item['icon']">
                                        <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                                            <flux:icon.question-mark-circle variant="micro" class="text-amber-500" />
                                            <span class="flex-1 [ui-selected_&]:hidden">{{ $isTurkish ? 'Mevcut özel ikon' : 'Current custom icon' }}</span>
                                            <code class="text-xs text-zinc-400 [ui-selected_&]:hidden">{{ $item['icon'] }}</code>
                                        </div>
                                    </flux:select.option>
                                @endif

                                @foreach ($iconCatalog as $name => $icon)
                                    <flux:select.option :value="$name">
                                        <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                                            <flux:icon :name="$icon['render'] ?? $name" variant="micro" class="text-zinc-500" />
                                            <span class="flex-1 [ui-selected_&]:hidden">{{ $icon[$activeLang] }}</span>
                                            <code class="text-xs text-zinc-400 [ui-selected_&]:hidden">{{ $name }}</code>
                                        </div>
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <div class="min-w-0 flex-1">
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Başlık' : 'Title'"
                                    wire:model="features.{{ $activeLang }}.{{ $rowKey }}.title"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Açıklama' : 'Description'"
                                    wire:model="features.{{ $activeLang }}.{{ $rowKey }}.description"
                                />
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-1">
                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="chevron-up"
                                wire:click="moveItem('features', '{{ $rowKey }}', -1)"
                                :disabled="$position === 0"
                            />
                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="chevron-down"
                                wire:click="moveItem('features', '{{ $rowKey }}', 1)"
                                :disabled="$position === count($repeaterOrder['features']) - 1"
                            />
                        </div>

                        <flux:button
                            size="xs"
                            variant="ghost"
                            icon="trash"
                            class="shrink-0 text-red-500 hover:text-red-700"
                            wire:click="removeItem('features', '{{ $rowKey }}')"
                            wire:confirm="Bu satırı silmek istediğinize emin misiniz?"
                        />
                    </div>
                @empty
                    <div class="rounded-lg border-2 border-dashed border-zinc-200 py-8 text-center dark:border-zinc-700">
                        <flux:icon.inbox class="mx-auto size-8 text-zinc-300 dark:text-zinc-600" />
                        <flux:text class="mt-2 text-sm text-zinc-400">Henüz bir kayıt eklenmedi.</flux:text>
                    </div>
                @endforelse
            </div>
        </flux:card>

        <flux:card>
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.milestone class="size-5 text-zinc-500" />
                    <flux:heading size="lg">{{ $isTurkish ? 'Teknik Kararlar' : 'Technical Decisions' }}</flux:heading>
                    <flux:badge size="sm" color="zinc">{{ count($technical_decisions[$activeLang]) }}</flux:badge>
                </div>
                <flux:button size="sm" variant="primary" icon="plus" wire:click="addItem('technical_decisions')">
                    Ekle
                </flux:button>
            </div>

            <div class="space-y-3">
                @forelse ($repeaterOrder['technical_decisions'] as $position => $rowKey)
                    @php($item = $technical_decisions[$activeLang][$rowKey])
                    <div
                        class="flex items-start gap-2 rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800"
                        wire:key="technical_decisions-{{ $activeLang }}-{{ $rowKey }}"
                    >
                        <div class="flex min-w-0 flex-1 items-center gap-2">
                            <div class="min-w-0 flex-1">
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Etiket' : 'Label'"
                                    wire:model="technical_decisions.{{ $activeLang }}.{{ $rowKey }}.label"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <flux:input
                                    size="sm"
                                    :placeholder="$isTurkish ? 'Değer' : 'Value'"
                                    wire:model="technical_decisions.{{ $activeLang }}.{{ $rowKey }}.value"
                                />
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-1">
                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="chevron-up"
                                wire:click="moveItem('technical_decisions', '{{ $rowKey }}', -1)"
                                :disabled="$position === 0"
                            />
                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="chevron-down"
                                wire:click="moveItem('technical_decisions', '{{ $rowKey }}', 1)"
                                :disabled="$position === count($repeaterOrder['technical_decisions']) - 1"
                            />
                        </div>

                        <flux:button
                            size="xs"
                            variant="ghost"
                            icon="trash"
                            class="shrink-0 text-red-500 hover:text-red-700"
                            wire:click="removeItem('technical_decisions', '{{ $rowKey }}')"
                            wire:confirm="Bu satırı silmek istediğinize emin misiniz?"
                        />
                    </div>
                @empty
                    <div class="rounded-lg border-2 border-dashed border-zinc-200 py-8 text-center dark:border-zinc-700">
                        <flux:icon.inbox class="mx-auto size-8 text-zinc-300 dark:text-zinc-600" />
                        <flux:text class="mt-2 text-sm text-zinc-400">Henüz bir kayıt eklenmedi.</flux:text>
                    </div>
                @endforelse
            </div>
        </flux:card>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon.chart-column class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ $isTurkish ? 'Proje Metrikleri' : 'Project Metrics' }}</flux:heading>
                <flux:badge size="sm" color="zinc">{{ count($metrics[$activeLang]) }}</flux:badge>
            </div>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="addItem('metrics')">
                Ekle
            </flux:button>
        </div>

        <div class="space-y-3">
            @forelse ($repeaterOrder['metrics'] as $position => $rowKey)
                @php($item = $metrics[$activeLang][$rowKey])
                @php($selectedIcon = $iconCatalog[$item['icon'] ?? ''] ?? null)
                <div
                    class="flex items-start gap-2 rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800"
                    wire:key="metrics-{{ $activeLang }}-{{ $rowKey }}"
                >
                    <div class="flex min-w-0 flex-1 items-center gap-2">
                        <flux:select
                            variant="listbox"
                            searchable
                            size="sm"
                            placeholder="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}"
                            empty="{{ $isTurkish ? 'Eşleşen ikon bulunamadı.' : 'No matching icon found.' }}"
                            class="w-10! shrink-0"
                            wire:model.live="metrics.{{ $activeLang }}.{{ $rowKey }}.icon"
                        >
                            <x-slot name="button">
                                <flux:select.button
                                    size="sm"
                                    class="w-10! justify-center px-2! [&>svg:last-child]:hidden"
                                    title="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}"
                                    aria-label="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}"
                                >
                                    <flux:select.selected placeholder="{{ $isTurkish ? 'İkon seç' : 'Select icon' }}" class="justify-center" />
                                </flux:select.button>
                            </x-slot>

                            <x-slot name="search">
                                <flux:select.search placeholder="{{ $isTurkish ? 'İkon ara...' : 'Search icons...' }}" />
                            </x-slot>

                            <flux:select.option value="">
                                <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                                    <flux:icon.x-mark variant="micro" class="text-zinc-400" />
                                    <span class="flex-1 [ui-selected_&]:hidden">{{ $isTurkish ? 'İkon yok' : 'No icon' }}</span>
                                </div>
                            </flux:select.option>

                            @if (($item['icon'] ?? '') !== '' && ! $selectedIcon)
                                <flux:select.option :value="$item['icon']">
                                    <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                                        <flux:icon.question-mark-circle variant="micro" class="text-amber-500" />
                                        <span class="flex-1 [ui-selected_&]:hidden">{{ $isTurkish ? 'Mevcut özel ikon' : 'Current custom icon' }}</span>
                                        <code class="text-xs text-zinc-400 [ui-selected_&]:hidden">{{ $item['icon'] }}</code>
                                    </div>
                                </flux:select.option>
                            @endif

                            @foreach ($iconCatalog as $name => $icon)
                                <flux:select.option :value="$name">
                                    <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                                        <flux:icon :name="$icon['render'] ?? $name" variant="micro" class="text-zinc-500" />
                                        <span class="flex-1 [ui-selected_&]:hidden">{{ $icon[$activeLang] }}</span>
                                        <code class="text-xs text-zinc-400 [ui-selected_&]:hidden">{{ $name }}</code>
                                    </div>
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <div class="min-w-0 flex-1">
                            <flux:input
                                size="sm"
                                :placeholder="$isTurkish ? 'Değer' : 'Value'"
                                wire:model="metrics.{{ $activeLang }}.{{ $rowKey }}.value"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <flux:input
                                size="sm"
                                :placeholder="$isTurkish ? 'Açıklama' : 'Label'"
                                wire:model="metrics.{{ $activeLang }}.{{ $rowKey }}.label"
                            />
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col gap-1">
                        <flux:button
                            size="xs"
                            variant="ghost"
                            icon="chevron-up"
                            wire:click="moveItem('metrics', '{{ $rowKey }}', -1)"
                            :disabled="$position === 0"
                        />
                        <flux:button
                            size="xs"
                            variant="ghost"
                            icon="chevron-down"
                            wire:click="moveItem('metrics', '{{ $rowKey }}', 1)"
                            :disabled="$position === count($repeaterOrder['metrics']) - 1"
                        />
                    </div>

                    <flux:button
                        size="xs"
                        variant="ghost"
                        icon="trash"
                        class="shrink-0 text-red-500 hover:text-red-700"
                        wire:click="removeItem('metrics', '{{ $rowKey }}')"
                        wire:confirm="Bu satırı silmek istediğinize emin misiniz?"
                    />
                </div>
            @empty
                <div class="rounded-lg border-2 border-dashed border-zinc-200 py-8 text-center dark:border-zinc-700">
                    <flux:icon.inbox class="mx-auto size-8 text-zinc-300 dark:text-zinc-600" />
                    <flux:text class="mt-2 text-sm text-zinc-400">Henüz bir kayıt eklenmedi.</flux:text>
                </div>
            @endforelse
        </div>
    </flux:card>
</form>
