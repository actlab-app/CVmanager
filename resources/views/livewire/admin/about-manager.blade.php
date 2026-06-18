@php($isTurkish = $activeLang === 'tr')

<form wire:submit="save" class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-700">
                <flux:icon.user-circle class="size-5 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl">Hakkımda Yönetimi</flux:heading>
                <flux:text class="text-sm text-zinc-500">Hakkımda sayfasının metinlerini, kartlarını ve hero görselini yönetin.</flux:text>
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
                    <flux:input label="Prensipler Başlığı" wire:model="translations.{{ $activeLang }}.principles_title" />
                    <flux:textarea label="Alıntı" wire:model="translations.{{ $activeLang }}.quote" rows="3" />
                    <flux:input label="Alıntı İmzası" wire:model="translations.{{ $activeLang }}.quote_attribution" />
                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:input label="Portfolio Butonu" wire:model="translations.{{ $activeLang }}.portfolio_cta" />
                        <flux:input label="İletişim Butonu" wire:model="translations.{{ $activeLang }}.contact_cta" />
                    </div>
                </div>
            </flux:card>
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
                <div class="mb-5">
                    <flux:heading size="lg">Hero Görseli</flux:heading>
                    <flux:text class="mt-1 text-xs text-zinc-500">Panoramik görseller üç panelde daha iyi sonuç verir.</flux:text>
                </div>

                <label class="block cursor-pointer overflow-hidden rounded-xl border-2 border-dashed border-zinc-200 transition hover:border-accent dark:border-zinc-700">
                    @if ($heroImage)
                        <img class="aspect-[3/1] w-full object-cover" src="{{ $heroImage->temporaryUrl() }}" alt="" />
                    @elseif ($existingHeroImagePath && ! $removeExistingHeroImage)
                        <img class="aspect-[3/1] w-full object-cover" src="{{ asset($existingHeroImagePath) }}" alt="" />
                    @else
                        <span class="flex aspect-[3/1] flex-col items-center justify-center text-zinc-400">
                            <flux:icon.cloud-arrow-up class="size-8" />
                            <span class="mt-2 text-sm font-semibold">Görsel seç</span>
                        </span>
                    @endif
                    <input type="file" class="sr-only" wire:model="heroImage" accept="image/jpeg,image/png,image/webp" />
                </label>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <flux:text class="text-xs text-zinc-400">JPG, PNG veya WebP · maksimum 8 MB</flux:text>
                    @if ($heroImage || ($existingHeroImagePath && ! $removeExistingHeroImage))
                        <flux:button type="button" size="xs" variant="ghost" icon="trash" class="text-red-500" wire:click="removeHeroImage">
                            Kaldır
                        </flux:button>
                    @endif
                </div>
                <flux:error name="heroImage" />
            </flux:card>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-admin.portfolio-repeater
            field="hero_panels"
            :items="$hero_panels[$activeLang]"
            :order="$repeaterOrder['hero_panels']"
            :lang="$activeLang"
            title="Hero Panelleri"
            icon="rectangle-group"
            :fields="['number' => 'Numara', 'title' => 'Başlık']"
        />

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

    <x-admin.portfolio-repeater
        field="principles"
        :items="$principles[$activeLang]"
        :order="$repeaterOrder['principles']"
        :lang="$activeLang"
        title="Çalışma Prensipleri"
        icon="list-bullet"
        :fields="['number' => 'Numara', 'text' => 'Prensip']"
    />
</form>
