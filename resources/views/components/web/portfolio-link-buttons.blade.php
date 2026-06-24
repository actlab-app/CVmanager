@props([
    'project',
    'showMissing' => true,
    'showWarning' => false,
    'attached' => false,
])

@php
    $hasLiveUrl = filled($project->live_url);
    $hasRepositoryUrl = filled($project->repository_url);
    $liveButtonClass = 'flex h-14 min-w-0 items-center justify-start rounded-bl-2xl pr-9 text-left text-[11px] font-black transition sm:text-[12px]';
    $repoButtonClass = 'flex h-14 min-w-0 items-center justify-end rounded-br-2xl pl-9 text-right text-[11px] font-black transition sm:text-[12px]';
    $activeLiveClass = $liveButtonClass.' bg-accent text-white hover:bg-accentDark';
    $activeRepoClass = $repoButtonClass.' bg-[#24292f] text-white hover:bg-[#151b22]';
    $passiveLiveClass = $liveButtonClass.' cursor-not-allowed bg-red-50 text-red-700 opacity-75 dark:bg-red-950/30 dark:text-red-200';
    $passiveRepoClass = $repoButtonClass.' cursor-not-allowed bg-zinc-100 text-zinc-500 opacity-75 dark:bg-zinc-900 dark:text-zinc-400';
    $liveIconCellClass = 'mr-3 flex h-14 w-14 shrink-0 items-center justify-center rounded-bl-2xl bg-white/10 shadow-[10px_0_18px_rgba(0,0,0,0.13)]';
    $repoIconCellClass = 'ml-3 flex h-14 w-14 shrink-0 items-center justify-center rounded-br-2xl bg-white/10 shadow-[-10px_0_18px_rgba(0,0,0,0.16)]';
    $passiveLiveIconCellClass = 'mr-3 flex h-14 w-14 shrink-0 items-center justify-center rounded-bl-2xl bg-red-100/80 shadow-[10px_0_18px_rgba(248,113,113,0.18)] dark:bg-red-950/40';
    $passiveRepoIconCellClass = 'ml-3 flex h-14 w-14 shrink-0 items-center justify-center rounded-br-2xl bg-zinc-200/70 shadow-[-10px_0_18px_rgba(63,63,70,0.16)] dark:bg-zinc-800/80';
@endphp

<div {{ $attributes->class('relative overflow-visible') }}>
    @if ($showWarning && (! $hasLiveUrl || ! $hasRepositoryUrl))
        <div class="grid grid-cols-[48px_1px_1fr] overflow-hidden rounded-t-xl border border-red-200 bg-red-50 text-[11px] font-semibold leading-snug text-red-700 shadow-sm dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
            <div class="flex min-h-full items-center justify-center bg-red-100/80 dark:bg-red-950/50">
                <img class="h-7 w-7 object-contain" src="{{ asset('images/icons/alert.svg') }}" alt="" />
            </div>
            <div class="bg-[linear-gradient(to_bottom,transparent_0%,rgba(248,113,113,0.45)_50%,transparent_100%)]"></div>
            <div class="px-3 py-2.5 text-[13px] text-center">
                {{ __('Bu proje açık kaynaklı değildir.') }}
                    <br>
                {{ __('Detaylar için iletişime geçiniz.') }}

                
            </div>
        </div>
    @endif

    <div @class([
        'relative grid grid-cols-2 border-line',
        'border-t' => $attached,
        'overflow-hidden rounded-xl border' => ! $attached,
    ])>
        @if ($hasLiveUrl)
            <a href="{{ $project->live_url }}" class="{{ $activeLiveClass }}" target="_blank" rel="noreferrer">
                <span class="{{ $liveIconCellClass }}">
                    <img class="h-6 w-6 object-contain brightness-0 invert" src="{{ asset('images/icons/live.svg') }}" alt="" />
                </span>
                <span class="min-w-0 truncate">{{ __('Canlı Önizleme') }}</span>
            </a>
        @elseif ($showMissing)
            <span class="{{ $passiveLiveClass }}" aria-disabled="true" title="{{ __('Canlı bağlantı paylaşılmadı') }}">
                <span class="{{ $passiveLiveIconCellClass }}">
                    <img class="h-6 w-6 object-contain opacity-70 grayscale" src="{{ asset('images/icons/live.svg') }}" alt="" />
                </span>
                <span class="min-w-0 truncate">{{ __('Canlı Önizleme') }}</span>
            </span>
        @endif

        @if ($hasRepositoryUrl)
            <a href="{{ $project->repository_url }}" class="{{ $activeRepoClass }}" target="_blank" rel="noreferrer">
                <span class="min-w-0 truncate">{{ __('Github Repo') }}</span>
                <span class="{{ $repoIconCellClass }}">
                    <img class="h-6 w-6 object-contain brightness-0 invert" src="{{ asset('images/icons/github.svg') }}" alt="" />
                </span>
            </a>
        @elseif ($showMissing)
            <span class="{{ $passiveRepoClass }}" aria-disabled="true" title="{{ __('Repository bağlantısı paylaşılmadı') }}">
                <span class="min-w-0 truncate">{{ __('Github Repo') }}</span>
                <span class="{{ $passiveRepoIconCellClass }}">
                    <img class="h-6 w-6 object-contain opacity-60 grayscale" src="{{ asset('images/icons/github.svg') }}" alt="" />
                </span>
            </span>
        @endif

        <a
            href="{{ \App\Support\ReferenceUrl::route('portfolio.show', $project) }}"
            class="absolute left-1/2 top-1/2 z-10 flex h-14 w-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-line bg-[var(--bg-card)] text-accent shadow-[0_12px_28px_rgba(15,23,42,0.18),inset_0_2px_0_rgba(255,255,255,0.55)] transition hover:scale-105 hover:text-accentDark dark:shadow-[0_12px_28px_rgba(0,0,0,0.35),inset_0_1px_0_rgba(255,255,255,0.08)]"
            aria-label="{{ __('Projeyi İncele') }}"
        >
            <img class="h-6 w-6 object-contain" src="{{ asset('images/icons/detail.svg') }}" alt="" />
        </a>
    </div>
</div>
