<?php

use App\Livewire\Admin\TechnologyManager;
use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

afterEach(function () {
    File::deleteDirectory(public_path('images/technologies/catalog'));
});

it('requires authentication for the technology catalog', function () {
    auth()->logout();

    $this->get(route('technology-manager.index'))->assertRedirect('/admin');
});

it('renders technologies with unsupported Flux icon names', function () {
    PortfolioTechnology::create([
        'name' => 'Image Tool',
        'slug' => 'image-tool',
        'category' => 'Media',
        'icon' => 'image',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    Livewire::test(TechnologyManager::class)
        ->assertSee('Image Tool')
        ->assertStatus(200);
});

it('creates a technology and uploads its logo to public', function () {
    $logo = UploadedFile::fake()->image('inertia.png', 128, 128);

    Livewire::test(TechnologyManager::class)
        ->set('name', 'Inertia.js')
        ->set('slug', 'inertia')
        ->set('category', 'Frontend')
        ->set('icon', 'component')
        ->set('sort_order', 10)
        ->set('logo', $logo)
        ->call('save')
        ->assertHasNoErrors();

    $technology = PortfolioTechnology::where('slug', 'inertia')->firstOrFail();

    expect($technology->name)->toBe('Inertia.js')
        ->and($technology->logo_path)->toStartWith('images/technologies/catalog/')
        ->and(public_path($technology->logo_path))->toBeFile();
});

it('updates and deletes managed technology logos', function () {
    $directory = public_path('images/technologies/catalog');
    File::ensureDirectoryExists($directory);
    File::put($directory.'/old.png', 'old-logo');

    $technology = PortfolioTechnology::create([
        'name' => 'Alpine.js',
        'slug' => 'alpinejs',
        'category' => 'Interaction',
        'logo_path' => 'images/technologies/catalog/old.png',
        'is_active' => true,
        'sort_order' => 1,
    ]);
    $project = PortfolioProject::create([
        'slug' => 'technology-test',
        'title' => ['tr' => 'Test', 'en' => 'Test'],
        'technologies' => ['alpinejs'],
    ]);

    Livewire::test(TechnologyManager::class)
        ->call('edit', $technology->id)
        ->set('name', 'Alpine JS')
        ->set('slug', 'alpine-js')
        ->set('logo', UploadedFile::fake()->image('new.png', 128, 128))
        ->call('save')
        ->assertHasNoErrors();

    $technology->refresh();

    expect($technology->name)->toBe('Alpine JS')
        ->and(public_path('images/technologies/catalog/old.png'))->not->toBeFile()
        ->and(public_path($technology->logo_path))->toBeFile()
        ->and($project->fresh()->technologies)->toBe(['alpine-js']);

    Livewire::test(TechnologyManager::class)
        ->call('delete', $technology->id);

    expect(PortfolioTechnology::find($technology->id))->toBeNull()
        ->and(public_path($technology->logo_path))->not->toBeFile()
        ->and($project->fresh()->technologies)->toBe([]);
});
