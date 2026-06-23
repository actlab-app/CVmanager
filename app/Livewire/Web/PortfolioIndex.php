<?php

namespace App\Livewire\Web;

use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class PortfolioIndex extends Component
{
    private const LANGUAGES = ['tr', 'en'];

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
        return view('livewire.web.portfolio-index', [
            'projects' => PortfolioProject::query()
                ->where('is_published', true)
                ->with('images')
                ->orderBy('sort_order')
                ->orderByDesc('project_date')
                ->orderBy('id')
                ->get(),
            'technologyCatalog' => PortfolioTechnology::query()->active()->get()->keyBy('slug'),
        ])->layout('components.layouts.web', [
            'title' => __('Portfolyo'),
        ]);
    }
}
