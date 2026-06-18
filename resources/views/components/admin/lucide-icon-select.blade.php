@props([
    'value' => '',
    'lang' => 'tr',
])

@php
    $icons = config('cv-icons');
    $selected = $icons[$value] ?? null;
    $selectLabel = $lang === 'tr' ? 'İkon seç' : 'Select icon';
    $searchLabel = $lang === 'tr' ? 'İkon ara...' : 'Search icons...';
    $emptyLabel = $lang === 'tr' ? 'Eşleşen ikon bulunamadı.' : 'No matching icon found.';
    $noneLabel = $lang === 'tr' ? 'İkon yok' : 'No icon';
    $unknownLabel = $lang === 'tr' ? 'Mevcut özel ikon' : 'Current custom icon';
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

    <flux:select.option value="">
        <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
            <flux:icon.x-mark variant="micro" class="text-zinc-400" />
            <span class="flex-1 [ui-selected_&]:hidden">{{ $noneLabel }}</span>
        </div>
    </flux:select.option>

    @if ($value !== '' && ! $selected)
        <flux:select.option :value="$value">
            <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                <flux:icon.question-mark-circle variant="micro" class="text-amber-500" />
                <span class="flex-1 [ui-selected_&]:hidden">{{ $unknownLabel }}</span>
                <code class="text-xs text-zinc-400 [ui-selected_&]:hidden">{{ $value }}</code>
            </div>
        </flux:select.option>
    @endif

    @foreach ($icons as $name => $icon)
        <flux:select.option :value="$name">
            <div class="flex min-w-56 items-center gap-2 [ui-selected_&]:min-w-0 [ui-selected_&]:gap-0">
                <flux:icon :name="$icon['render'] ?? $name" variant="micro" class="text-zinc-500" />
                <span class="flex-1 [ui-selected_&]:hidden">{{ $icon[$lang] }}</span>
                <code class="text-xs text-zinc-400 [ui-selected_&]:hidden">{{ $name }}</code>
            </div>
        </flux:select.option>
    @endforeach
</flux:select>
