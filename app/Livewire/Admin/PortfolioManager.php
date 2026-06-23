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
    public function moveProject(int $projectId, int $direction): void
    {
        if (! in_array($direction, [-1, 1], true)) {
            return;
        }

        $projects = PortfolioProject::query()
            ->orderBy('sort_order')
            ->orderByDesc('project_date')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $projects->search(fn (PortfolioProject $project): bool => $project->id === $projectId);

        if ($index === false) {
            return;
        }

        $target = $index + $direction;

        if (! $projects->has($target)) {
            return;
        }

        [$projects[$index], $projects[$target]] = [$projects[$target], $projects[$index]];

        foreach ($projects->values() as $sortOrder => $project) {
            $project->forceFill(['sort_order' => $sortOrder])->saveQuietly();
        }

        Flux::toast('Sıralama güncellendi.', variant: 'success');
    }

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
                ->orderBy('id')
                ->get(),
        ]);
    }
}
