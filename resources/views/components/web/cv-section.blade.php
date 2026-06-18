@props([
    'title',
    'icon',
    'items',
    'labelKey',
    'valueKey',
    'compact' => false,
])

<div class="rounded-2xl bg-[var(--bg-card)] p-1">
    <div
        class="mb-3 flex items-center gap-2 rounded-lg bg-accentSoft px-3 py-2 text-[12px] font-black tracking-wide text-accentDark sm:mb-4 sm:px-4 sm:text-[13px]"
    >
        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--bg-card)] text-[17px]">
            <i data-lucide="{{ $icon }}"></i>
        </span>
        {{ $title }}
    </div>

    <div
        @class([
            'overflow-hidden rounded-xl border border-line leading-snug',
            'text-[11.5px] sm:text-[12.5px]' => $compact,
            'text-[12px] sm:text-[13px]' => ! $compact,
        ])
    >
        @foreach ($items as $item)
            <div
                @class([
                    'grid grid-cols-[110px_1fr] sm:grid-cols-[132px_1fr]',
                    'bg-row' => $loop->odd,
                    'bg-[var(--bg-card)]' => $loop->even,
                ])
            >
                <div class="flex items-center gap-2 px-3 py-2 font-extrabold text-muted">
                    <i data-lucide="{{ data_get($item, 'icon', 'circle') }}"></i>
                    {{ data_get($item, $labelKey, '') }}
                </div>
                <div class="px-3 py-2 text-ink">{{ data_get($item, $valueKey, '') }}</div>
            </div>
        @endforeach
    </div>
</div>
