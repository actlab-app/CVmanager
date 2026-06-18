<?php

namespace App\Livewire\Admin;

use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use App\Support\TechnologyIcon;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Teknoloji Kataloğu')]
class TechnologyManager extends Component
{
    use WithFileUploads;

    public ?int $technologyId = null;

    public string $name = '';

    public string $slug = '';

    public string $category = '';

    public string $icon = 'code-bracket';

    public bool $is_active = true;

    public int $sort_order = 0;

    public mixed $logo = null;

    public ?string $existingLogoPath = null;

    public bool $removeExistingLogo = false;

    public function edit(int $technologyId): void
    {
        $technology = PortfolioTechnology::findOrFail($technologyId);

        $this->technologyId = $technology->id;
        $this->name = $technology->name;
        $this->slug = $technology->slug;
        $this->category = $technology->category ?? '';
        $this->icon = $technology->icon ?? 'code-bracket';
        $this->is_active = (bool) $technology->is_active;
        $this->sort_order = (int) $technology->sort_order;
        $this->existingLogoPath = $technology->logo_path;
        $this->removeExistingLogo = false;
        $this->logo = null;
        $this->resetValidation();
    }

    public function create(): void
    {
        $this->resetForm();
    }

    public function removeLogo(): void
    {
        $this->logo = null;
        $this->removeExistingLogo = true;
    }

    public function save(): void
    {
        $this->slug = Str::slug($this->slug ?: $this->name);
        $validated = $this->validate();

        $technology = $this->technologyId
            ? PortfolioTechnology::findOrFail($this->technologyId)
            : new PortfolioTechnology;

        $oldSlug = $technology->slug;
        $oldLogoPath = $technology->logo_path;
        $newLogoPath = $oldLogoPath;

        if ($this->removeExistingLogo) {
            $newLogoPath = null;
        }

        if ($this->logo) {
            $directory = public_path('images/technologies/catalog');
            File::ensureDirectoryExists($directory);

            $extension = strtolower($this->logo->getClientOriginalExtension() ?: 'png');
            $filename = Str::uuid().'.'.$extension;
            File::copy($this->logo->getRealPath(), $directory.DIRECTORY_SEPARATOR.$filename);
            $newLogoPath = 'images/technologies/catalog/'.$filename;
        }

        $technology->fill([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'category' => $validated['category'] ?: null,
            'icon' => $validated['icon'] ?: null,
            'logo_path' => $newLogoPath,
            'is_active' => $validated['is_active'],
            'sort_order' => $validated['sort_order'],
        ])->save();

        if ($oldSlug && $oldSlug !== $technology->slug) {
            $this->replaceProjectTechnologySlug($oldSlug, $technology->slug);
        }

        if ($oldLogoPath && $oldLogoPath !== $newLogoPath) {
            $technology->setAttribute('logo_path', $oldLogoPath);
            $technology->deleteManagedLogo();
            $technology->setAttribute('logo_path', $newLogoPath);
        }

        Flux::toast($this->technologyId ? 'Teknoloji güncellendi.' : 'Teknoloji eklendi.', variant: 'success');

        $this->resetForm();
    }

    public function delete(int $technologyId): void
    {
        $technology = PortfolioTechnology::findOrFail($technologyId);

        $this->removeTechnologyFromProjects($technology->slug);
        $technology->delete();

        if ($this->technologyId === $technologyId) {
            $this->resetForm();
        }

        Flux::toast('Teknoloji silindi.', variant: 'success');
    }

    public function render(): View
    {
        $technologies = PortfolioTechnology::query()->ordered()->get();

        $technologies->each(function (PortfolioTechnology $technology): void {
            $technology->setAttribute('render_icon', TechnologyIcon::resolve($technology->icon));
        });

        return view('livewire.admin.technology-manager', [
            'technologies' => $technologies,
        ]);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:100',
                Rule::unique('portfolio_technologies', 'slug')->ignore($this->technologyId),
            ],
            'category' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'technologyId',
            'name',
            'slug',
            'category',
            'logo',
            'existingLogoPath',
            'removeExistingLogo',
        ]);

        $this->icon = 'code-bracket';
        $this->is_active = true;
        $this->sort_order = (int) PortfolioTechnology::max('sort_order') + 1;
        $this->resetValidation();
    }

    private function replaceProjectTechnologySlug(string $oldSlug, string $newSlug): void
    {
        PortfolioProject::query()
            ->whereJsonContains('technologies', $oldSlug)
            ->get()
            ->each(function (PortfolioProject $project) use ($oldSlug, $newSlug): void {
                $project->technologies = array_values(array_map(
                    fn (string $slug): string => $slug === $oldSlug ? $newSlug : $slug,
                    $project->technologies ?? [],
                ));
                $project->save();
            });
    }

    private function removeTechnologyFromProjects(string $slugToRemove): void
    {
        PortfolioProject::query()
            ->whereJsonContains('technologies', $slugToRemove)
            ->get()
            ->each(function (PortfolioProject $project) use ($slugToRemove): void {
                $project->technologies = array_values(array_filter(
                    $project->technologies ?? [],
                    fn (string $slug): bool => $slug !== $slugToRemove,
                ));
                $project->save();
            });
    }
}
