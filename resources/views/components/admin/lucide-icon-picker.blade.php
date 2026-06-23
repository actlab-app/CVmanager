@props([
    'lang' => 'tr',
])

@php
    $icons = config('cv-icons');
@endphp

<div
    x-data="{
        open: false,
        target: '',
        value: '',
        lang: @js($lang),
        search: '',
        labels: {
            tr: {
                title: 'İkon seç',
                search: 'İkon ara...',
                empty: 'Eşleşen ikon bulunamadı.',
                none: 'İkon yok',
                close: 'Kapat',
            },
            en: {
                title: 'Select icon',
                search: 'Search icons...',
                empty: 'No matching icon found.',
                none: 'No icon',
                close: 'Close',
            },
        },
        matches(name, tr, en) {
            const term = this.search.trim().toLowerCase();

            if (! term) return true;

            return name.toLowerCase().includes(term)
                || tr.toLowerCase().includes(term)
                || en.toLowerCase().includes(term);
        },
        choose(icon) {
            if (! this.target) return;

            this.$wire.set(this.target, icon);
            this.open = false;
        },
    }"
    x-on:open-lucide-icon-picker.window="
        target = $event.detail.model;
        value = $event.detail.value || '';
        lang = $event.detail.lang || @js($lang);
        search = '';
        open = true;
        $nextTick(() => $refs.search?.focus());
    "
    x-on:keydown.escape.window="open = false"
    x-cloak
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-50 bg-black/40"
        x-on:click="open = false"
    ></div>

    <div
        x-show="open"
        x-transition
        class="fixed left-1/2 top-1/2 z-50 w-[min(720px,calc(100vw-2rem))] max-w-3xl -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900"
        role="dialog"
        aria-modal="true"
        x-on:click.stop
    >
        <div class="flex items-center justify-between gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <flux:icon.sparkles class="size-4 text-zinc-500" />
                <div class="text-sm font-bold text-zinc-800 dark:text-zinc-100" x-text="labels[lang]?.title || labels.tr.title"></div>
            </div>
            <button
                type="button"
                class="rounded-lg p-1.5 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white"
                x-on:click="open = false"
                :aria-label="labels[lang]?.close || labels.tr.close"
                :title="labels[lang]?.close || labels.tr.close"
            >
                <flux:icon.x-mark class="size-4" />
            </button>
        </div>

        <div class="border-b border-zinc-200 p-3 dark:border-zinc-700">
            <flux:input
                x-ref="search"
                x-model.debounce.120ms="search"
                size="sm"
                icon="magnifying-glass"
                x-bind:placeholder="labels[lang]?.search || labels.tr.search"
            />
        </div>

        <div class="max-h-[420px] overflow-y-auto p-3">
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                <button
                    type="button"
                    class="flex min-w-0 items-center gap-2 rounded-lg border px-2.5 py-2 text-left text-sm transition hover:border-accent hover:text-accent"
                    x-bind:class="value === '' ? 'border-accent bg-accent/10 text-accent' : 'border-zinc-200 text-zinc-600 dark:border-zinc-700 dark:text-zinc-300'"
                    x-show="matches('', labels.tr.none, labels.en.none)"
                    x-on:click="choose('')"
                >
                    <flux:icon.x-mark variant="micro" class="shrink-0 text-zinc-400" />
                    <span class="truncate" x-text="labels[lang]?.none || labels.tr.none"></span>
                </button>

                @foreach ($icons as $name => $icon)
                    <button
                        type="button"
                        class="flex min-w-0 items-center gap-2 rounded-lg border px-2.5 py-2 text-left text-sm transition hover:border-accent hover:text-accent"
                        x-bind:class="value === @js($name) ? 'border-accent bg-accent/10 text-accent' : 'border-zinc-200 text-zinc-600 dark:border-zinc-700 dark:text-zinc-300'"
                        x-show="matches(@js($name), @js($icon['tr']), @js($icon['en']))"
                        x-on:click="choose(@js($name))"
                    >
                        <flux:icon :name="$icon['render'] ?? $name" variant="micro" class="shrink-0" />
                        <span class="min-w-0 flex-1 truncate">{{ $icon[$lang] }}</span>
                        <code class="hidden text-[10px] text-zinc-400 sm:inline">{{ $name }}</code>
                    </button>
                @endforeach
            </div>

            <div
                class="py-8 text-center text-sm text-zinc-400"
                x-show="![...$el.previousElementSibling.children].some((item) => item.style.display !== 'none')"
                x-text="labels[lang]?.empty || labels.tr.empty"
            ></div>
        </div>
    </div>
</div>
