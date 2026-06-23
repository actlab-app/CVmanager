@php($isTurkish = $activeLang === 'tr')

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-700">
                <flux:icon.document-text class="size-5 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl">CV Yönetimi</flux:heading>
                <flux:text class="text-sm text-zinc-500">Özgeçmiş bilgilerini buradan düzenleyebilirsiniz.</flux:text>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:badge color="zinc" size="sm" class="mr-1">
                <flux:icon.language class="size-3.5!" />
                Dil
            </flux:badge>
            <flux:button size="sm" :variant="$isTurkish ? 'primary' : 'ghost'" wire:click="switchLang('tr')">
                🇹🇷 Türkçe
            </flux:button>
            <flux:button size="sm" :variant="$isTurkish ? 'ghost' : 'primary'" wire:click="switchLang('en')">
                🇬🇧 English
            </flux:button>
        </div>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center gap-2">
            <flux:icon.user class="size-5 text-zinc-500" />
            <flux:heading size="lg">Genel Bilgiler</flux:heading>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input label="Ad Soyad" wire:model="full_name" placeholder="Tam ad..." icon="user" />
                <flux:input
                    label="QR Linki"
                    wire:model="qr_url"
                    placeholder="https://example.com/portfolio"
                    icon="qr-code"
                />
            </div>

            <flux:input
                :label="$isTurkish ? 'Meslek Ünvanı (TR)' : 'Job Title (EN)'"
                wire:model="translations.{{ $activeLang }}.job_title"
                :placeholder="$isTurkish ? 'Meslek ünvanı...' : 'Job title...'"
                icon="briefcase"
            />

            <flux:textarea
                :label="$isTurkish ? 'Hakkında İçerik (TR) - HTML destekli' : 'About Content (EN) - HTML supported'"
                wire:model="translations.{{ $activeLang }}.about_content"
                :placeholder="$isTurkish ? 'Hakkında içeriği (HTML)...' : 'About content (HTML)...'"
                rows="4"
            />
        </div>
    </flux:card>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="lg:relative lg:h-full lg:min-h-0">
            <div class="space-y-6 lg:absolute lg:inset-0 lg:overflow-y-auto lg:pr-2">
                <x-admin.cv-repeater
                    field="quick_infos"
                    :items="$quick_infos[$activeLang]"
                    :order="$repeaterOrder['quick_infos']"
                    :lang="$activeLang"
                    :title="$isTurkish ? 'Hızlı Bilgiler' : 'Quick Info'"
                    icon="list-bullet"
                    primary-field="title"
                    :primary-placeholder="$isTurkish ? 'Başlık' : 'Title'"
                    secondary-field="value"
                    :secondary-placeholder="$isTurkish ? 'Değer' : 'Value'"
                />
            </div>
        </div>

        <div class="space-y-6">
            <x-admin.cv-repeater
                field="educations"
                :items="$educations[$activeLang]"
                :order="$repeaterOrder['educations']"
                :lang="$activeLang"
                :title="$isTurkish ? 'Eğitim Bilgileri' : 'Education'"
                icon="academic-cap"
                primary-field="degree"
                :primary-placeholder="$isTurkish ? 'Derece' : 'Degree'"
                secondary-field="school"
                :secondary-placeholder="$isTurkish ? 'Okul' : 'School'"
            />

            <x-admin.cv-repeater
                field="experiences"
                :items="$experiences[$activeLang]"
                :order="$repeaterOrder['experiences']"
                :lang="$activeLang"
                :title="$isTurkish ? 'Profesyonel Deneyim' : 'Professional Experience'"
                icon="building-office"
                primary-field="company"
                :primary-placeholder="$isTurkish ? 'Şirket' : 'Company'"
                secondary-field="description"
                :secondary-placeholder="$isTurkish ? 'Açıklama' : 'Description'"
            />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-admin.cv-repeater
            field="skills"
            :items="$skills[$activeLang]"
            :order="$repeaterOrder['skills']"
            :lang="$activeLang"
            :title="$isTurkish ? 'Teknik Yetkinlik' : 'Technical Skills'"
            icon="cpu-chip"
            primary-field="category"
            :primary-placeholder="$isTurkish ? 'Kategori' : 'Category'"
            secondary-field="details"
            :secondary-placeholder="$isTurkish ? 'Detaylar' : 'Details'"
        />

        <x-admin.cv-repeater
            field="project_types"
            :items="$project_types[$activeLang]"
            :order="$repeaterOrder['project_types']"
            :lang="$activeLang"
            :title="$isTurkish ? 'Proje Tipleri' : 'Project Types'"
            icon="folder"
            primary-field="type"
            :primary-placeholder="$isTurkish ? 'Tip' : 'Type'"
            secondary-field="description"
            :secondary-placeholder="$isTurkish ? 'Açıklama' : 'Description'"
        />
    </div>

    <div
        class="flex items-center justify-end gap-3 rounded-xl border border-zinc-200 bg-zinc-50 px-5 py-4 dark:border-zinc-700 dark:bg-zinc-800"
    >
        <flux:text class="text-sm text-zinc-400">
            Tüm dillerdeki veriler aynı anda kaydedilir.
        </flux:text>
        <flux:button variant="primary" icon="check" wire:click="save" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">Kaydet</span>
            <span wire:loading wire:target="save">Kaydediliyor...</span>
        </flux:button>
    </div>

    <x-admin.lucide-icon-picker :lang="$activeLang" />
</div>
