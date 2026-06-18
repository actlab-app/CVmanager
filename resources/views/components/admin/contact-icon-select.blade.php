@props([
    'value' => 'link',
    'lang' => 'tr',
])

@php
    $icons = config('contact-icons');
    $selectLabel = $lang === 'tr' ? 'İkon seç' : 'Select icon';
    $searchLabel = $lang === 'tr' ? 'İkon ara...' : 'Search icons...';
    $emptyLabel = $lang === 'tr' ? 'Eşleşen ikon bulunamadı.' : 'No matching icon found.';
@endphp

<flux:select
    variant="listbox"
    searchable
    size="sm"
    :placeholder="$selectLabel"
    :empty="$emptyLabel"
    {{ $attributes->class('w-10! shrink-0') }}
>
    <x-slot name="button">
        <flux:select.button
            size="sm"
            class="w-10! justify-center px-2! [&>svg:last-child]:hidden"
            :title="$selectLabel"
            :aria-label="$selectLabel"
        >
            <flux:select.selected :placeholder="$selectLabel" class="justify-center" />
        </flux:select.button>
    </x-slot>

    <x-slot name="search">
        <flux:select.search :placeholder="$searchLabel" />
    </x-slot>

    @foreach ($icons as $name => $icon)
        <flux:select.option :value="$name">
            <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                <x-contact-icon :name="$name" class="size-4 text-zinc-500" />
                <span class="flex-1 [ui-selected_&]:hidden">{{ $icon[$lang] }}</span>
                <code class="text-xs text-zinc-400 [ui-selected_&]:hidden">{{ $name }}</code>
            </div>
        </flux:select.option>
    @endforeach
</flux:select>
