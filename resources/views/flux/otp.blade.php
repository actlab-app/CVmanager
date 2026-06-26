@props([
    'length' => 6,
    'label' => null,
])

@php
    $inputClasses = 'block w-full max-w-48 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-center text-lg font-semibold tracking-[0.35em] text-zinc-900 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-accent focus:ring-2 focus:ring-accent/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder:text-zinc-500';
@endphp

<div class="inline-flex w-full max-w-48 flex-col gap-2">
    @if ($label)
        <label @class([
            'text-sm font-medium text-zinc-700 dark:text-zinc-200' => ! $attributes->has('label:sr-only'),
            'sr-only' => $attributes->has('label:sr-only'),
        ])>
            {{ $label }}
        </label>
    @endif

    <input
        type="text"
        inputmode="numeric"
        autocomplete="one-time-code"
        maxlength="{{ (int) $length }}"
        {{ $attributes->except(['label:sr-only'])->class($inputClasses) }}
    />
</div>
