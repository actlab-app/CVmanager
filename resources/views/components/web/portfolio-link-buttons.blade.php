@props([
    'project',
    'compact' => false,
])

@php
    $baseClass = $compact
        ? 'inline-flex h-9 min-w-0 flex-1 items-center justify-center gap-1.5 rounded-lg px-2.5 text-[11px] font-black transition'
        : 'inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-[12px] font-black transition';
    $liveClass = $baseClass.' bg-accent text-white shadow-sm hover:bg-accentDark';
    $repoClass = $baseClass.' border border-line bg-soft text-ink hover:border-accent hover:text-accentDark';
    $hiddenClass = $baseClass.' cursor-not-allowed border border-red-200 bg-red-50 text-red-600 opacity-85 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300';
@endphp

<div {{ $attributes->class('grid grid-cols-2 gap-2') }}>
    @if ($project->live_url)
        <a href="{{ $project->live_url }}" class="{{ $liveClass }}" target="_blank" rel="noreferrer">
            <i data-lucide="external-link" class="h-4 w-4"></i>
            <span class="truncate">{{ __('Canlı') }}</span>
        </a>
    @else
        <span class="{{ $hiddenClass }}" aria-disabled="true" title="{{ __('Canlı bağlantı paylaşılmadı') }}">
            <i data-lucide="lock" class="h-4 w-4"></i>
            <span class="truncate">{{ __('Gizli') }}</span>
        </span>
    @endif

    @if ($project->repository_url)
        <a href="{{ $project->repository_url }}" class="{{ $repoClass }}" target="_blank" rel="noreferrer">
            <i data-lucide="github" class="h-4 w-4"></i>
            <span class="truncate">{{ __('GitHub') }}</span>
        </a>
    @else
        <span class="{{ $hiddenClass }}" aria-disabled="true" title="{{ __('Repository bağlantısı paylaşılmadı') }}">
            <i data-lucide="github" class="h-4 w-4"></i>
            <span class="truncate">{{ __('Gizli') }}</span>
        </span>
    @endif
</div>
