@props([
    'name' => 'link',
])

@php
    $icon = config('contact-icons.'.$name) ?? config('contact-icons.link');
@endphp

@if ($icon['type'] === 'brand')
    <span
        aria-hidden="true"
        {{ $attributes->class('inline-block shrink-0 bg-current') }}
        style="-webkit-mask: url('{{ asset($icon['path']) }}') center / contain no-repeat; mask: url('{{ asset($icon['path']) }}') center / contain no-repeat;"
    ></span>
@else
    <flux:icon :name="$icon['icon']" {{ $attributes }} />
@endif
