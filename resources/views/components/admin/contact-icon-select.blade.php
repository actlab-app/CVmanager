@props([
    'value' => 'link',
    'lang' => 'tr',
])

@php
    $icons = config('contact-icons');
    $selectLabel = $lang === 'tr' ? 'İkon seç' : 'Select icon';
    $currentValue = array_key_exists($value, $icons) ? $value : 'link';
@endphp

<div
    {{ $attributes->whereDoesntStartWith('wire:model')->class('relative h-8 w-10 shrink-0') }}
>
    <select
        {{ $attributes->whereStartsWith('wire:model') }}
        class="absolute inset-0 z-10 h-8 w-10 cursor-pointer opacity-0"
        title="{{ $selectLabel }}"
        aria-label="{{ $selectLabel }}"
    >
        @foreach ($icons as $name => $icon)
            <option value="{{ $name }}" @selected($name === $currentValue)>
                {{ $icon[$lang] ?? $name }} ({{ $name }})
            </option>
        @endforeach
    </select>

    <div class="pointer-events-none flex h-8 w-10 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-600 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
        <x-contact-icon :name="$currentValue" class="size-4" />
    </div>

    <div class="hidden" aria-hidden="true">
        @foreach ($icons as $name => $icon)
            <x-contact-icon :name="$name" class="size-4" />
        @endforeach
    </div>
</div>
