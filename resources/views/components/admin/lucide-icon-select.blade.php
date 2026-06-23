@props([
    'value' => '',
    'lang' => 'tr',
])

@php
    $icons = config('cv-icons');
    $selected = $icons[$value] ?? null;
    $model = $attributes->wire('model')->value();
    $buttonLabel = $lang === 'tr' ? 'İkon seç' : 'Select icon';
    $unknownLabel = $lang === 'tr' ? 'Mevcut özel ikon' : 'Current custom icon';
    $buttonAttributes = $attributes
        ->whereDoesntStartWith('wire:model')
        ->class('inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 transition hover:border-accent hover:text-accent dark:border-zinc-700 dark:bg-zinc-900');
@endphp

<span class="inline-flex shrink-0 items-center">
    <input type="hidden" {{ $attributes->whereStartsWith('wire:model') }} />

    <button
        type="button"
        {{ $buttonAttributes }}
        title="{{ $buttonLabel }}"
        aria-label="{{ $buttonLabel }}"
        onclick="window.dispatchEvent(new CustomEvent('open-lucide-icon-picker', { detail: { model: @js($model), value: @js($value), lang: @js($lang) } }))"
    >
        @if ($value === '')
            <flux:icon.x-mark variant="micro" class="text-zinc-400" />
        @elseif ($selected)
            <flux:icon :name="$selected['render'] ?? $value" variant="micro" />
        @else
            <flux:icon.question-mark-circle variant="micro" class="text-amber-500" />
            <span class="sr-only">{{ $unknownLabel }}: {{ $value }}</span>
        @endif
    </button>
</span>
