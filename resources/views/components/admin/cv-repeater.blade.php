@props([
    'field',
    'items',
    'order',
    'lang',
    'title',
    'icon',
    'primaryField',
    'primaryPlaceholder',
    'secondaryField',
    'secondaryPlaceholder',
    'tertiaryField' => null,
    'tertiaryPlaceholder' => null,
])

@php
    $addLabel = $lang === 'tr' ? 'Ekle' : 'Add';
    $emptyLabel = $lang === 'tr' ? 'Henüz bir öğe eklenmedi.' : 'No items added yet.';
    $deleteConfirmation = $lang === 'tr'
        ? 'Bu öğeyi silmek istediğinize emin misiniz?'
        : 'Are you sure you want to delete this item?';
@endphp

<flux:card>
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:icon :name="$icon" class="size-5 text-zinc-500" />
            <flux:heading size="lg">{{ $title }}</flux:heading>
            <flux:badge size="sm" color="zinc">{{ count($items) }}</flux:badge>
        </div>

        <flux:button type="button" size="sm" variant="primary" icon="plus" wire:click="addItem('{{ $field }}')">
            {{ $addLabel }}
        </flux:button>
    </div>

    <div class="space-y-4">
        @forelse ($order as $position => $rowKey)
            @php
                $item = $items[$rowKey];
            @endphp

            <div
                class="group flex items-start gap-2 rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800"
                wire:key="{{ $field }}-{{ $lang }}-{{ $rowKey }}"
            >
                <div class="flex min-w-0 flex-1 items-start gap-2">
                    <x-admin.lucide-icon-select
                        :value="$item['icon'] ?? ''"
                        :lang="$lang"
                        wire:model.live="{{ $field }}.{{ $lang }}.{{ $rowKey }}.icon"
                    />

                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="grid gap-2 md:grid-cols-2">
                            <flux:input
                                size="sm"
                                :placeholder="$primaryPlaceholder"
                                wire:model="{{ $field }}.{{ $lang }}.{{ $rowKey }}.{{ $primaryField }}"
                            />

                            <flux:input
                                size="sm"
                                :placeholder="$secondaryPlaceholder"
                                wire:model="{{ $field }}.{{ $lang }}.{{ $rowKey }}.{{ $secondaryField }}"
                            />
                        </div>

                        @if ($tertiaryField)
                            <flux:textarea
                                :placeholder="$tertiaryPlaceholder"
                                wire:model="{{ $field }}.{{ $lang }}.{{ $rowKey }}.{{ $tertiaryField }}"
                                rows="3"
                            />
                        @endif
                    </div>
                </div>

                <div class="flex shrink-0 flex-col gap-1">
                    <flux:button
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="chevron-up"
                        wire:click="moveItemUp('{{ $field }}', '{{ $rowKey }}')"
                        :disabled="$position === 0"
                    />
                    <flux:button
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="chevron-down"
                        wire:click="moveItemDown('{{ $field }}', '{{ $rowKey }}')"
                        :disabled="$position === count($order) - 1"
                    />
                </div>

                <flux:button
                    type="button"
                    size="xs"
                    variant="ghost"
                    icon="trash"
                    class="shrink-0 text-red-500 hover:text-red-700"
                    wire:click="removeItem('{{ $field }}', '{{ $rowKey }}')"
                    :wire:confirm="$deleteConfirmation"
                />
            </div>
        @empty
            <div class="rounded-lg border-2 border-dashed border-zinc-200 py-8 text-center dark:border-zinc-700">
                <flux:icon.inbox class="mx-auto size-8 text-zinc-300 dark:text-zinc-600" />
                <flux:text class="mt-2 text-sm text-zinc-400">{{ $emptyLabel }}</flux:text>
            </div>
        @endforelse
    </div>
</flux:card>
