<?php

use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use Database\Seeders\RedeskPortfolioSeeder;

it('seeds the complete RE-DESK showcase project idempotently without screenshots', function () {
    $this->seed(RedeskPortfolioSeeder::class);
    $this->seed(RedeskPortfolioSeeder::class);

    $project = PortfolioProject::query()
        ->where('slug', 're-desk')
        ->with('images')
        ->firstOrFail();

    expect(PortfolioProject::query()->where('slug', 're-desk')->count())->toBe(1)
        ->and($project->getTranslation('title', 'tr'))->toBe('RE-DESK')
        ->and($project->getTranslation('project_type', 'tr'))->toContain('Yapay Zekâ')
        ->and($project->getTranslation('features', 'tr'))->toHaveCount(10)
        ->and($project->getTranslation('technical_decisions', 'en'))->toHaveCount(10)
        ->and($project->getTranslation('metrics', 'tr'))->toHaveCount(4)
        ->and($project->technologies)->toHaveCount(20)
        ->and($project->is_featured)->toBeTrue()
        ->and($project->is_showcase)->toBeTrue()
        ->and($project->is_published)->toBeTrue()
        ->and($project->images)->toBeEmpty();

    $technologies = PortfolioTechnology::query()
        ->whereIn('slug', $project->technologies)
        ->get();

    expect($technologies)->toHaveCount(20)
        ->and($technologies->every(
            fn (PortfolioTechnology $technology): bool => filled($technology->logo_path) || filled($technology->icon),
        ))->toBeTrue();

    $portfolioCopy = collect([
        'short_description',
        'detailed_description',
        'project_type',
        'role',
        'duration',
        'platform',
        'features',
        'technical_decisions',
        'metrics',
    ])->map(fn (string $field): array => $project->getTranslations($field))->toJson();

    expect(mb_strtolower($portfolioCopy))
        ->not->toContain('pilot')
        ->not->toContain('demo');
});
