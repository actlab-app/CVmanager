<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-700">
                <flux:icon.folder class="size-5 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl">Portfolio Yönetimi</flux:heading>
                <flux:text class="text-sm text-zinc-500">Projeleri, görselleri ve teknik içerikleri yönetin.</flux:text>
            </div>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('portfolio-manager.create')" wire:navigate>
            Yeni Proje
        </flux:button>
    </div>

    <div class="grid gap-4">
        @forelse ($projects as $project)
            <flux:card class="p-0!">
                <div class="flex flex-col gap-4 p-4 md:flex-row md:items-center">
                    <div class="h-28 w-full shrink-0 overflow-hidden rounded-xl bg-zinc-100 md:w-44 dark:bg-zinc-800">
                        @if ($project->images->first())
                            <img
                                class="h-full w-full object-cover"
                                src="{{ asset($project->images->first()->path) }}"
                                alt=""
                            />
                        @else
                            <div class="flex h-full items-center justify-center">
                                <flux:icon.photo class="size-8 text-zinc-300 dark:text-zinc-600" />
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:heading size="lg">{{ $project->getTranslation('title', 'tr', false) }}</flux:heading>
                            <flux:badge :color="$project->is_published ? 'green' : 'zinc'" size="sm">
                                {{ $project->is_published ? 'Yayında' : 'Taslak' }}
                            </flux:badge>
                            @if ($project->is_featured)
                                <flux:badge color="amber" size="sm">Öne Çıkan</flux:badge>
                            @endif
                        </div>
                        <flux:text class="mt-1 line-clamp-2 text-sm text-zinc-500">
                            {{ $project->getTranslation('short_description', 'tr', false) }}
                        </flux:text>
                        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-400">
                            <span class="flex items-center gap-1">
                                <flux:icon.link variant="micro" />
                                {{ $project->slug }}
                            </span>
                            <span class="flex items-center gap-1">
                                <flux:icon.photo variant="micro" />
                                {{ $project->images_count }} görsel
                            </span>
                            <span class="flex items-center gap-1">
                                <flux:icon.calendar variant="micro" />
                                {{ $project->project_date?->format('Y') ?? '-' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <flux:icon.queue-list variant="micro" />
                                Sıra {{ $project->sort_order }}
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <div class="flex items-center rounded-lg border border-zinc-200 bg-white p-1 dark:border-zinc-700 dark:bg-zinc-900">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="chevron-up"
                                wire:click="moveProject({{ $project->id }}, -1)"
                                :disabled="$loop->first"
                                title="Yukarı taşı"
                            />
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="chevron-down"
                                wire:click="moveProject({{ $project->id }}, 1)"
                                :disabled="$loop->last"
                                title="Aşağı taşı"
                            />
                        </div>

                        @if ($project->is_published)
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="arrow-top-right-on-square"
                                :href="route('portfolio.show', $project)"
                                target="_blank"
                            >
                                Görüntüle
                            </flux:button>
                        @endif
                        <flux:button
                            size="sm"
                            variant="filled"
                            icon="pencil-square"
                            :href="route('portfolio-manager.edit', $project)"
                            wire:navigate
                        >
                            Düzenle
                        </flux:button>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            class="text-red-500 hover:text-red-700"
                            wire:click="delete({{ $project->id }})"
                            wire:confirm="Bu projeyi ve yüklenen görsellerini silmek istediğinize emin misiniz?"
                        />
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:card class="py-14 text-center">
                <flux:icon.folder-plus class="mx-auto size-10 text-zinc-300 dark:text-zinc-600" />
                <flux:heading size="lg" class="mt-3">Henüz proje eklenmedi</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500">İlk portfolio projenizi oluşturarak başlayın.</flux:text>
            </flux:card>
        @endforelse
    </div>
</div>
