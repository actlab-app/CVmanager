@php($isTurkish = $activeLang === 'tr')

<form wire:submit="save" class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-700">
                <flux:icon.user-circle class="size-5 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl">Hakkımda Yönetimi</flux:heading>
                <flux:text class="text-sm text-zinc-500">Hakkımda sayfasının metinlerini, kartlarını ve hero görsellerini yönetin.</flux:text>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button type="button" size="sm" :variant="$isTurkish ? 'primary' : 'ghost'" wire:click="switchLang('tr')">Türkçe</flux:button>
            <flux:button type="button" size="sm" :variant="$isTurkish ? 'ghost' : 'primary'" wire:click="switchLang('en')">English</flux:button>
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

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(340px,0.7fr)]">
        <div class="space-y-6">
            <flux:card>
                <div class="mb-5 flex items-center gap-2">
                    <flux:icon.document-text class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Ana İçerik - {{ strtoupper($activeLang) }}</flux:heading>
                </div>

                <div class="space-y-4">
                    <flux:input label="Üst Etiket" wire:model="translations.{{ $activeLang }}.eyebrow" />
                    <flux:textarea label="Ana Başlık" wire:model="translations.{{ $activeLang }}.headline" rows="3" />
                    <flux:textarea label="Giriş Metni" wire:model="translations.{{ $activeLang }}.intro" rows="3" />

                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:input label="Durum Etiketi" wire:model="translations.{{ $activeLang }}.current_label" />
                        <flux:input label="Durum" wire:model="translations.{{ $activeLang }}.current_status" />
                    </div>
                    <flux:textarea label="Güncel Çalışma Metni" wire:model="translations.{{ $activeLang }}.current_text" rows="2" />
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-5 flex items-center gap-2">
                    <flux:icon.magnifying-glass class="size-5 text-zinc-500" />
                    <flux:heading size="lg">Yaklaşım Metni</flux:heading>
                </div>

                <div class="space-y-4">
                    <flux:input label="Başlık" wire:model="translations.{{ $activeLang }}.philosophy_title" />
                    <flux:textarea label="Açıklama" wire:model="translations.{{ $activeLang }}.philosophy_text" rows="4" />
                    <flux:textarea label="Alıntı" wire:model="translations.{{ $activeLang }}.quote" rows="3" />
                    <flux:input label="Alıntı İmzası" wire:model="translations.{{ $activeLang }}.quote_attribution" />
                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:input label="Portfolio Butonu" wire:model="translations.{{ $activeLang }}.portfolio_cta" />
                        <flux:input label="İletişim Butonu" wire:model="translations.{{ $activeLang }}.contact_cta" />
                    </div>
                </div>
            </flux:card>

            <x-admin.portfolio-repeater
                field="focus_cards"
                :items="$focus_cards[$activeLang]"
                :order="$repeaterOrder['focus_cards']"
                :lang="$activeLang"
                title="Bilgi Kartları"
                icon="squares-2x2"
                :fields="['title' => 'Başlık', 'text' => 'Kısa metin']"
                icon-picker
            />
        </div>

        <div class="space-y-6">
            <flux:card>
                <div class="mb-5">
                    <flux:heading size="lg">Profil Görseli</flux:heading>
                    <flux:text class="mt-1 text-xs text-zinc-500">Başlığın solunda yuvarlak biçimde gösterilir.</flux:text>
                </div>

                <label class="mx-auto block size-40 cursor-pointer overflow-hidden rounded-full border-4 border-dashed border-emerald-500/40 bg-zinc-100 transition hover:border-emerald-500 dark:bg-zinc-800">
                    @if ($profileImage)
                        <img class="h-full w-full object-cover" src="{{ $profileImage->temporaryUrl() }}" alt="" />
                    @elseif ($existingProfileImagePath && ! $removeExistingProfileImage)
                        <img class="h-full w-full object-cover" src="{{ asset($existingProfileImagePath) }}" alt="" />
                    @else
                        <span class="flex h-full w-full flex-col items-center justify-center text-zinc-400">
                            <flux:icon.user class="size-9" />
                            <span class="mt-2 text-xs font-semibold">Fotoğraf seç</span>
                        </span>
                    @endif
                    <input type="file" class="sr-only" wire:model="profileImage" accept="image/jpeg,image/png,image/webp" />
                </label>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <flux:text class="text-xs text-zinc-400">Kare görsel önerilir · maksimum 4 MB</flux:text>
                    @if ($profileImage || ($existingProfileImagePath && ! $removeExistingProfileImage))
                        <flux:button type="button" size="xs" variant="ghost" icon="trash" class="text-red-500" wire:click="removeProfileImage">
                            Kaldır
                        </flux:button>
                    @endif
                </div>
                <flux:error name="profileImage" />

                <div class="mt-4 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <flux:checkbox wire:model="profileIsPersonal" label="Bu görseli kişisel veri olarak işaretle" description="Dashboard'da gizlilik modu açıldığında görsel maskelenir." />
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-5 flex items-center gap-2">
                    <flux:icon.photo class="size-5 text-zinc-500" />
                    <div>
                        <flux:heading size="lg">Hero Görselleri - {{ strtoupper($activeLang) }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500">Her panel için ayrı görsel, başlık ve açıklama girin.</flux:text>
                    </div>
                </div>

                <div class="grid gap-4">
                    @foreach ($hero_showcases[$activeLang] as $index => $item)
                        <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700" wire:key="hero-showcase-{{ $activeLang }}-{{ $index }}">
                            <label class="block cursor-pointer overflow-hidden rounded-lg border-2 border-dashed border-zinc-200 transition hover:border-accent dark:border-zinc-700">
                                @if ($heroShowcaseUploads[$index] ?? null)
                                    <img class="aspect-[16/9] w-full object-cover" src="{{ $heroShowcaseUploads[$index]->temporaryUrl() }}" alt="" />
                                @elseif (! empty($item['image_path']))
                                    <img class="aspect-[16/9] w-full object-cover" src="{{ asset($item['image_path']) }}" alt="" />
                                @else
                                    <span class="flex aspect-[16/9] flex-col items-center justify-center text-zinc-400">
                                        <flux:icon.cloud-arrow-up class="size-8" />
                                        <span class="mt-2 text-sm font-semibold">Görsel seç</span>
                                    </span>
                                @endif
                                <input type="file" class="sr-only" wire:model="heroShowcaseUploads.{{ $index }}" accept="image/jpeg,image/png,image/webp" />
                            </label>

                            <div class="mt-3 grid gap-3">
                                <flux:input label="Başlık" wire:model="hero_showcases.{{ $activeLang }}.{{ $index }}.title" />
                                <flux:textarea label="Açıklama" wire:model="hero_showcases.{{ $activeLang }}.{{ $index }}.description" rows="2" />
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3">
                                <flux:text class="text-xs text-zinc-400">JPG, PNG veya WebP · maksimum 8 MB</flux:text>
                                @if (($heroShowcaseUploads[$index] ?? null) || ! empty($item['image_path']))
                                    <flux:button type="button" size="xs" variant="ghost" icon="trash" class="text-red-500" wire:click="removeHeroShowcaseImage({{ $index }})">
                                        Kaldır
                                    </flux:button>
                                @endif
                            </div>
                            <flux:error name="heroShowcaseUploads.{{ $index }}" />
                        </div>
                    @endforeach
                </div>
            </flux:card>
        </div>
    </div>

    <x-admin.lucide-icon-picker :lang="$activeLang" />
</form>
