<?php

use App\Livewire\Web\PortfolioProject as PortfolioProjectPage;
use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use Livewire\Livewire;

function createPublishedPortfolioProject(array $attributes = []): PortfolioProject
{
    foreach ([
        ['slug' => 'laravel', 'name' => 'Laravel 12', 'category' => 'Backend', 'sort_order' => 0],
        ['slug' => 'livewire', 'name' => 'Livewire 3', 'category' => 'UI State', 'sort_order' => 1],
        ['slug' => 'tailwindcss', 'name' => 'Tailwind CSS 4', 'category' => 'Styling', 'sort_order' => 2],
    ] as $technology) {
        PortfolioTechnology::query()->firstOrCreate(
            ['slug' => $technology['slug']],
            [...$technology, 'is_active' => true],
        );
    }

    $project = new PortfolioProject([
        'slug' => $attributes['slug'] ?? 'cv-manager',
        'status' => $attributes['status'] ?? 'active',
        'project_date' => $attributes['project_date'] ?? '2026-06-13',
        'live_url' => $attributes['live_url'] ?? null,
        'repository_url' => $attributes['repository_url'] ?? null,
        'technologies' => ['laravel', 'livewire', 'tailwindcss'],
        'is_featured' => $attributes['is_featured'] ?? false,
        'is_published' => $attributes['is_published'] ?? true,
        'sort_order' => $attributes['sort_order'] ?? 0,
    ]);

    foreach ([
        'title' => $attributes['title'] ?? 'CV Manager',
        'short_description' => 'Çok dilli portfolio projesi.',
        'detailed_description' => 'Dinamik proje detay sayfası.',
        'project_type' => 'Portfolio Yönetim Aracı',
        'role' => 'Full-Stack Developer',
        'duration' => '2 hafta',
        'platform' => 'Responsive web',
    ] as $field => $value) {
        $project->setTranslation($field, 'tr', $value);
        $project->setTranslation($field, 'en', $value);
    }

    foreach ([
        'features' => [['icon' => 'sparkles', 'title' => 'Responsive Tasarım', 'description' => 'Mobil uyumlu.']],
        'technical_decisions' => [['label' => 'Mimari', 'value' => 'Livewire']],
        'metrics' => [['icon' => 'languages', 'value' => '2', 'label' => 'Desteklenen dil']],
    ] as $field => $value) {
        $project->setTranslation($field, 'tr', $value);
        $project->setTranslation($field, 'en', $value);
    }

    $project->save();

    $image = $project->images()->create([
        'path' => 'images/portfolio/cv-manager/admin-dashboard.png',
        'sort_order' => 0,
    ]);
    $image->setTranslation('title', 'tr', 'Çok Dilli Yönetim Paneli');
    $image->setTranslation('title', 'en', 'Multilingual Admin Panel');
    $image->setTranslation('description', 'tr', 'Yönetim ekranı.');
    $image->setTranslation('description', 'en', 'Admin screen.');
    $image->save();

    return $project->fresh('images');
}

it('renders a published portfolio detail page', function () {
    $project = createPublishedPortfolioProject();

    $this->withSession(['locale' => 'tr'])
        ->get(route('portfolio.show', $project))
        ->assertOk()
        ->assertSee('CV Manager')
        ->assertSee('KULLANILAN TEKNOLOJİLER')
        ->assertSee('ÖNE ÇIKAN ÖZELLİKLER')
        ->assertSee('Tam ekran görüntüle')
        ->assertSeeHtml('x-teleport="body"')
        ->assertSeeHtml('aria-modal="true"')
        ->assertSee(asset('images/portfolio/cv-manager/admin-dashboard.png'));
});

it('lists published projects on the portfolio index', function () {
    $project = createPublishedPortfolioProject();

    $hiddenProject = createPublishedPortfolioProject(['slug' => 'hidden-project']);
    $hiddenProject->update(['is_published' => false]);

    $this->get(route('portfolio.index'))
        ->assertOk()
        ->assertSee('CV Manager')
        ->assertSee(route('portfolio.show', $project))
        ->assertDontSee(route('portfolio.show', $hiddenProject));
});

it('shows public and private portfolio links on index and detail pages', function () {
    $linkedProject = createPublishedPortfolioProject([
        'slug' => 'linked-project',
        'title' => 'Linked Project',
        'live_url' => 'https://example.com/live-project',
        'repository_url' => 'https://github.com/example/live-project',
    ]);

    createPublishedPortfolioProject([
        'slug' => 'private-project',
        'title' => 'Private Project',
    ]);

    $this->withSession(['locale' => 'tr'])
        ->get(route('portfolio.index'))
        ->assertOk()
        ->assertSee('https://example.com/live-project', false)
        ->assertSee('https://github.com/example/live-project', false)
        ->assertSee('Gizli');

    $this->withSession(['locale' => 'tr'])
        ->get(route('portfolio.show', $linkedProject))
        ->assertOk()
        ->assertSee('https://example.com/live-project', false)
        ->assertSee('https://github.com/example/live-project', false);
});

it('orders portfolio index projects by managed sort order', function () {
    createPublishedPortfolioProject([
        'slug' => 'featured-late',
        'title' => 'Featured Late',
        'is_featured' => true,
        'project_date' => '2026-06-13',
        'sort_order' => 20,
    ]);

    createPublishedPortfolioProject([
        'slug' => 'plain-early',
        'title' => 'Plain Early',
        'is_featured' => false,
        'project_date' => '2020-01-01',
        'sort_order' => 5,
    ]);

    $content = $this->get(route('portfolio.index'))
        ->assertOk()
        ->getContent();

    expect(strpos($content, 'Plain Early'))->toBeLessThan(strpos($content, 'Featured Late'));
});

it('returns not found for an unpublished project', function () {
    $project = createPublishedPortfolioProject();
    $project->update(['is_published' => false]);

    $this->get(route('portfolio.show', $project))->assertNotFound();
});

it('provides project data and technology stack', function () {
    $project = createPublishedPortfolioProject();

    Livewire::withQueryParams([])
        ->test(PortfolioProjectPage::class, ['project' => $project])
        ->assertSet('project.id', $project->id)
        ->assertSee('Çok Dilli Yönetim Paneli')
        ->assertSee('Laravel 12')
        ->assertSee('Responsive Tasarım');
});

it('ships all local portfolio assets', function () {
    foreach ([
        'admin-dashboard.png',
        'public-cv.png',
        'mobile-showcase.png',
    ] as $image) {
        expect(public_path('images/portfolio/cv-manager/'.$image))->toBeFile();
    }

    foreach (['laravel.svg', 'php.svg', 'livewire.svg', 'tailwindcss.svg', 'alpinejs.svg', 'vite.svg'] as $logo) {
        expect(public_path('images/technologies/'.$logo))->toBeFile();
    }
});
