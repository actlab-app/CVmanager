<?php

namespace App\Livewire\Web;

use App\Models\PortfolioProject as PortfolioProjectModel;
use App\Models\PortfolioTechnology;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class PortfolioProject extends Component
{
    private const LANGUAGES = ['tr', 'en'];

    public PortfolioProjectModel $project;

    public function mount(PortfolioProjectModel $project): void
    {
        abort_unless($project->is_published, 404);

        $this->project = $project->load('images');
    }

    public function setLocale(string $lang): void
    {
        if (! in_array($lang, self::LANGUAGES, true)) {
            return;
        }

        session()->put('locale', $lang);
        App::setLocale($lang);
        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function render(): View
    {
        $catalog = PortfolioTechnology::query()
            ->active()
            ->whereIn('slug', $this->project->technologies ?? [])
            ->get()
            ->keyBy('slug');
        $technologies = collect($this->project->technologies ?? [])
            ->map(fn (string $slug): ?PortfolioTechnology => $catalog->get($slug))
            ->filter()
            ->values();

        $statusLabels = [
            'draft' => __('Taslak'),
            'active' => __('Aktif Geliştirme'),
            'completed' => __('Tamamlandı'),
            'archived' => __('Arşivlendi'),
        ];

        return view('livewire.web.portfolio-project', [
            'technologies' => $technologies,
            'statusLabel' => $statusLabels[$this->project->status] ?? $this->project->status,
        ])->layout('components.layouts.web', [
            'title' => $this->project->title.' - '.__('Proje Detayı'),
        ]);
    }
}
