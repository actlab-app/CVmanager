@props([
    'field',
    'items',
    'order',
    'lang',
    'title',
    'icon',
    'fields',
    'iconPicker' => false,
])

<flux:card>
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:icon :name="$icon" class="size-5 text-zinc-500" />
            <flux:heading size="lg">{{ $title }}</flux:heading>
            <flux:badge size="sm" color="zinc">{{ count($items) }}</flux:badge>
        </div>
        <flux:button size="sm" variant="primary" icon="plus" wire:click="addItem('{{ $field }}')">
            Ekle
        </flux:button>
    </div>

    <div class="space-y-3">
        @forelse ($order as $position => $rowKey)
            @php($item = $items[$rowKey])
            <div
                class="flex items-start gap-2 rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800"
                wire:key="{{ $field }}-{{ $lang }}-{{ $rowKey }}"
            >
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    @if ($iconPicker)
                        <x-admin.lucide-icon-select
                            :value="$item['icon'] ?? ''"
                            :lang="$lang"
                            wire:model.live="{{ $field }}.{{ $lang }}.{{ $rowKey }}.icon"
                        />
                    @endif

                    @foreach ($fields as $name => $placeholder)
                        <div class="min-w-0 flex-1">
                            <flux:input
                                size="sm"
                                :placeholder="$placeholder"
                                wire:model="{{ $field }}.{{ $lang }}.{{ $rowKey }}.{{ $name }}"
                            />
                        </div>
                    @endforeach
                </div>

                <div class="flex shrink-0 flex-col gap-1">
                    <flux:button
                        size="xs"
                        variant="ghost"
                        icon="chevron-up"
                        wire:click="moveItem('{{ $field }}', '{{ $rowKey }}', -1)"
                        :disabled="$position === 0"
                    />
                    <flux:button
                        size="xs"
                        variant="ghost"
                        icon="chevron-down"
                        wire:click="moveItem('{{ $field }}', '{{ $rowKey }}', 1)"
                        :disabled="$position === count($order) - 1"
                    />
                </div>

                <flux:button
                    size="xs"
                    variant="ghost"
                    icon="trash"
                    class="shrink-0 text-red-500 hover:text-red-700"
                    wire:click="removeItem('{{ $field }}', '{{ $rowKey }}')"
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
