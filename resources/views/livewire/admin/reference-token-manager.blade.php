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

                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-900/60">
                        <div class="mb-2 text-xs font-bold uppercase tracking-wide text-zinc-500">Önizleme Linkleri</div>
                        <div class="space-y-2">
                            @foreach (['cv' => 'CV', 'portfolio.index' => 'Portfolyo', 'contact' => 'İletişim'] as $routeName => $label)
                                @php($previewLink = $this->referenceLink($routeName))
                                <div class="flex items-center gap-2 rounded-lg bg-white p-2 dark:bg-zinc-800">
                                    <div class="min-w-0 flex-1 truncate text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{ $label }}: {{ $previewLink }}</div>
                                    <button type="button" class="rounded-md p-1.5 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-700 dark:hover:text-white" title="Kopyala" onclick="navigator.clipboard?.writeText(@js($previewLink))">
                                        <flux:icon.clipboard class="size-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
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
                        <div class="flex min-w-0 gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                                @if ($referenceToken->image)
                                    <img class="h-full w-full object-cover" src="{{ asset($referenceToken->image) }}" alt="" />
                                @else
                                    <flux:icon.link class="size-6" />
                                @endif
                            </span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:heading size="lg">{{ $referenceToken->name }}</flux:heading>
                                    <flux:badge :color="$referenceToken->is_active ? 'green' : 'zinc'">
                                        {{ $referenceToken->is_active ? 'Aktif' : 'Pasif' }}
                                    </flux:badge>
                                </div>
                                <div class="mt-1 font-mono text-sm font-black text-zinc-900 dark:text-white">{{ $referenceToken->token }}</div>
                                @if ($referenceToken->description)
                                    <flux:text class="mt-2 text-sm text-zinc-500">{{ $referenceToken->description }}</flux:text>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
                            <flux:button type="button" size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $referenceToken->id }})">
                                Düzenle
                            </flux:button>
                            <flux:button type="button" size="sm" variant="danger" icon="trash" wire:click="delete({{ $referenceToken->id }})" wire:confirm="Bu referans tokeni ve ziyaret geçmişi silinsin mi?">
                                Sil
                            </flux:button>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 border-t border-zinc-200 pt-4 sm:grid-cols-3 dark:border-zinc-700">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide text-zinc-400">Toplam Ziyaret</div>
                            <div class="mt-1 text-2xl font-black text-zinc-900 dark:text-white">{{ $referenceToken->visits_count }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide text-zinc-400">Son Ziyaret</div>
                            <div class="mt-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                {{ $referenceToken->last_visited_at?->format('d.m.Y H:i') ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide text-zinc-400">Kayıt</div>
                            <div class="mt-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ $referenceToken->visit_records_count }} ziyaret kaydı</div>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        @foreach (['cv' => 'CV', 'portfolio.index' => 'Portfolyo', 'contact' => 'İletişim'] as $routeName => $label)
                            @php($link = ReferenceUrl::appendToken(route($routeName), $referenceToken->token))
                            <div class="flex items-center gap-2 rounded-xl border border-zinc-200 p-2 dark:border-zinc-700">
                                <span class="w-20 shrink-0 text-xs font-black text-zinc-500">{{ $label }}</span>
                                <span class="min-w-0 flex-1 truncate font-mono text-xs text-zinc-600 dark:text-zinc-300">{{ $link }}</span>
                                <button type="button" class="rounded-md p-1.5 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-700 dark:hover:text-white" title="Kopyala" onclick="navigator.clipboard?.writeText(@js($link))">
                                    <flux:icon.clipboard class="size-4" />
                                </button>
                            </div>
                        @endforeach
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
</div>
