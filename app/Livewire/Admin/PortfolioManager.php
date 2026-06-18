<?php

namespace App\Livewire\Admin;

use App\Models\PortfolioProject;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Portfolio Yönetimi')]
class PortfolioManager extends Component
{
    public function delete(int $projectId): void
    {
        $project = PortfolioProject::find($projectId);

        if (! $project) {
            return;
        }

        $project->delete();

        Flux::toast('Proje silindi.', variant: 'success');
    }

    public function render(): View
    {
        return view('livewire.admin.portfolio-manager', [
            'projects' => PortfolioProject::query()
                ->with('images')
                ->withCount('images')
                ->orderBy('sort_order')
                ->orderByDesc('project_date')
                ->get(),
        ]);
    }
}
