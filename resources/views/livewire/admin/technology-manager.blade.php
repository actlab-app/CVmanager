<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-700">
                <flux:icon.cpu-chip class="size-5 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl">Teknoloji Kataloğu</flux:heading>
                <flux:text class="text-sm text-zinc-500">Portfolio projelerinde kullanılabilecek teknolojileri yönetin.</flux:text>
            </div>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="create">
            Yeni Teknoloji
        </flux:button>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(340px,0.65fr)]">
        <div class="grid content-start gap-3 sm:grid-cols-2">
            @forelse ($technologies as $technology)
                <flux:card class="p-4!">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                            @if ($technology->logo_path)
                                <img class="h-7 w-7 object-contain" src="{{ asset($technology->logo_path) }}" alt="" />
                            @else
                                <flux:icon :name="$technology->render_icon" class="size-6 text-zinc-500" />
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:heading size="lg">{{ $technology->name }}</flux:heading>
                                <flux:badge :color="$technology->is_active ? 'green' : 'zinc'" size="sm">
                                    {{ $technology->is_active ? 'Aktif' : 'Pasif' }}
                                </flux:badge>
                            </div>
                            <flux:text class="mt-1 text-xs text-zinc-500">
                                {{ $technology->category ?: 'Kategorisiz' }} · {{ $technology->slug }}
                            </flux:text>
                        </div>

                        <div class="flex gap-1">
                            <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="edit({{ $technology->id }})" />
                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="trash"
                                class="text-red-500"
                                wire:click="delete({{ $technology->id }})"
                                wire:confirm="Bu teknolojiyi katalogdan silmek istediğinize emin misiniz?"
                            />
                        </div>
                    </div>
                </flux:card>
            @empty
                <flux:card class="col-span-full py-14 text-center">
                    <flux:icon.cpu-chip class="mx-auto size-10 text-zinc-300 dark:text-zinc-600" />
                    <flux:heading size="lg" class="mt-3">Teknoloji kaydı bulunmuyor</flux:heading>
                </flux:card>
            @endforelse
        </div>

        <form wire:submit="save">
            <flux:card class="sticky top-6">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <flux:heading size="lg">{{ $technologyId ? 'Teknolojiyi Düzenle' : 'Yeni Teknoloji' }}</flux:heading>
                        <flux:text class="mt-1 text-xs text-zinc-500">Logo yüklenmezse Lucide ikonu kullanılır.</flux:text>
                    </div>
                    @if ($technologyId)
                        <flux:button type="button" size="sm" variant="ghost" wire:click="create">Vazgeç</flux:button>
                    @endif
                </div>

                <div class="space-y-4">
                    <flux:input label="Teknoloji Adı" wire:model="name" placeholder="Laravel 12" />
                    <flux:input label="Slug" wire:model="slug" placeholder="laravel" />
                    <flux:input label="Kategori" wire:model="category" placeholder="Backend" />

                    <div class="grid grid-cols-[1fr_110px] gap-3">
                        <flux:input label="Lucide İkonu" wire:model="icon" placeholder="code-bracket" />
                        <flux:input type="number" min="0" label="Sıralama" wire:model="sort_order" />
                    </div>

                    <div>
                        <flux:label>Logo</flux:label>
                        <label class="mt-2 flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-zinc-200 p-3 transition hover:border-accent dark:border-zinc-700">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                @if ($logo)
                                    <img class="h-8 w-8 object-contain" src="{{ $logo->temporaryUrl() }}" alt="" />
                                @elseif ($existingLogoPath && ! $removeExistingLogo)
                                    <img class="h-8 w-8 object-contain" src="{{ asset($existingLogoPath) }}" alt="" />
                                @else
                                    <flux:icon.cloud-arrow-up class="size-6 text-zinc-400" />
                                @endif
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Logo seç</span>
                                <span class="block text-xs text-zinc-400">PNG, JPG, WebP veya SVG · maksimum 2 MB</span>
                            </span>
                            <input type="file" class="sr-only" wire:model="logo" accept=".png,.jpg,.jpeg,.webp,.svg" />
                        </label>

                        @if ($logo || ($existingLogoPath && ! $removeExistingLogo))
                            <flux:button type="button" size="xs" variant="ghost" icon="trash" class="mt-2 text-red-500" wire:click="removeLogo">
                                Logoyu kaldır
                            </flux:button>
                        @endif
                        <flux:error name="logo" />
                    </div>

                    <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                        <flux:checkbox wire:model="is_active" label="Proje seçimlerinde göster" />
                    </div>

                    <flux:button type="submit" variant="primary" icon="check" class="w-full">
                        {{ $technologyId ? 'Değişiklikleri Kaydet' : 'Teknolojiyi Ekle' }}
                    </flux:button>
                </div>
            </flux:card>
        </form>
    </div>
</div>
