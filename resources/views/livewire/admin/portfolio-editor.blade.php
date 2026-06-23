@php($isTurkish = $activeLang === 'tr')

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
        <x-admin.portfolio-repeater
            field="features"
            :items="$features[$activeLang]"
            :order="$repeaterOrder['features']"
            :lang="$activeLang"
            :title="$isTurkish ? 'Öne Çıkan Özellikler' : 'Highlighted Features'"
            icon="sparkles"
            :fields="[
                'title' => $isTurkish ? 'Başlık' : 'Title',
                'description' => $isTurkish ? 'Açıklama' : 'Description',
            ]"
            icon-picker
        />

        <x-admin.portfolio-repeater
            field="technical_decisions"
            :items="$technical_decisions[$activeLang]"
            :order="$repeaterOrder['technical_decisions']"
            :lang="$activeLang"
            :title="$isTurkish ? 'Teknik Kararlar' : 'Technical Decisions'"
            icon="milestone"
            :fields="[
                'label' => $isTurkish ? 'Etiket' : 'Label',
                'value' => $isTurkish ? 'Değer' : 'Value',
            ]"
        />
    </div>

    <x-admin.portfolio-repeater
        field="metrics"
        :items="$metrics[$activeLang]"
        :order="$repeaterOrder['metrics']"
        :lang="$activeLang"
        :title="$isTurkish ? 'Proje Metrikleri' : 'Project Metrics'"
        icon="chart-column"
        :fields="[
            'value' => $isTurkish ? 'Değer' : 'Value',
            'label' => $isTurkish ? 'Açıklama' : 'Label',
        ]"
        icon-picker
    />

    <x-admin.lucide-icon-picker :lang="$activeLang" />
</form>
