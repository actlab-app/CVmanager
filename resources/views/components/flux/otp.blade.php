@props([
    'name' => null,
    'length' => 6,
    'label' => null,
])

@php
    $labelSrOnly = $attributes->has('label:sr-only');
    $inputAttributes = $attributes->except(['class', 'label:sr-only']);
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label
            @class([
                'mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300',
                'sr-only' => $labelSrOnly,
            ])
        >
            {{ $label }}
        </label>
    @endif

    <input
        {{ $inputAttributes->merge([
            'type' => 'text',
            'inputmode' => 'numeric',
            'autocomplete' => 'one-time-code',
            'maxlength' => $length,
            'name' => $name,
            'class' => 'w-52 rounded-lg border border-stone-300 bg-white px-4 py-3 text-center font-mono text-xl font-semibold text-stone-900 shadow-sm outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100',
        ]) }}
        x-on:clear-2fa-auth-code.window="$el.value = ''; $dispatch('input', '')"
        x-on:focus-2fa-auth-code.window="$el.focus()"
    />
</div>
