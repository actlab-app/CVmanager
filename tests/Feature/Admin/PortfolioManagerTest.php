<?php

use App\Livewire\Admin\PortfolioEditor;
use App\Livewire\Admin\PortfolioManager;
use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    PortfolioTechnology::create([
        'name' => 'Laravel 12',
        'slug' => 'laravel',
        'category' => 'Backend',
        'is_active' => true,
        'sort_order' => 0,
    ]);
    PortfolioTechnology::create([
        'name' => 'Livewire 3',
        'slug' => 'livewire',
        'category' => 'UI State',
        'is_active' => true,
        'sort_order' => 1,
    ]);
});

afterEach(function () {
    File::deleteDirectory(public_path('images/portfolio/test-project'));
});

it('requires authentication for portfolio management pages', function () {
    auth()->logout();

    $this->get(route('portfolio-manager.index'))->assertRedirect('/admin');
    $this->get(route('portfolio-manager.create'))->assertRedirect('/admin');
});

it('renders the editor when a technology has an unsupported Flux icon', function () {
    PortfolioTechnology::create([
        'name' => 'Image Tool',
        'slug' => 'image-tool',
        'category' => 'Media',
        'icon' => 'image',
        'is_active' => true,
        'sort_order' => 2,
    ]);

    Livewire::test(PortfolioEditor::class)
        ->assertSee('Image Tool')
        ->assertStatus(200);
});

it('creates a multilingual project and uploads images directly to public', function () {
    $upload = UploadedFile::fake()->image('dashboard.jpg', 1200, 750);

    $component = Livewire::test(PortfolioEditor::class)
        ->set('slug', 'test-project')
        ->set('status', 'completed')
        ->set('project_date', '2026-06-13')
        ->set('translations.tr.title', 'Test Projesi')
        ->set('translations.en.title', 'Test Project')
        ->set('translations.tr.short_description', 'Türkçe açıklama')
        ->set('translations.en.short_description', 'English description')
        ->set('technologySlugs', ['laravel', 'livewire'])
        ->set('is_published', true)
        ->set('uploads', [$upload]);
    $uploadKey = $component->get('uploadOrder.0');

    $component
        ->set("uploadTranslations.{$uploadKey}.tr.title", 'Yönetim Paneli')
        ->set("uploadTranslations.{$uploadKey}.en.title", 'Admin Panel')
        ->call('save')
        ->assertHasNoErrors();

    $project = PortfolioProject::where('slug', 'test-project')->firstOrFail();
    $image = $project->images()->firstOrFail();

    expect($project->getTranslation('title', 'tr'))->toBe('Test Projesi')
        ->and($project->technologies)->toBe(['laravel', 'livewire'])
        ->and($image->getTranslation('title', 'en'))->toBe('Admin Panel')
        ->and(public_path($image->path))->toBeFile()
        ->and($image->path)->toStartWith('images/portfolio/test-project/');
});

it('keeps moved portfolio repeater rows isolated across languages', function () {
    $component = Livewire::test(PortfolioEditor::class)
        ->call('addItem', 'features')
        ->call('addItem', 'features');
    [$firstKey, $secondKey] = $component->get('repeaterOrder.features');

    $component
        ->set("features.tr.{$firstKey}.title", 'İlk Özellik')
        ->set("features.en.{$firstKey}.title", 'First Feature')
        ->set("features.tr.{$firstKey}.description", 'İlk açıklama')
        ->set("features.en.{$firstKey}.description", 'First description')
        ->set("features.tr.{$secondKey}.title", 'İkinci Özellik')
        ->set("features.en.{$secondKey}.title", 'Second Feature')
        ->set("features.tr.{$secondKey}.description", 'İkinci açıklama')
        ->set("features.en.{$secondKey}.description", 'Second description')
        ->assertSeeHtml('wire:model="features.tr.'.$secondKey.'.title"')
        ->call('moveItem', 'features', $secondKey, -1)
        ->assertSet('repeaterOrder.features.0', $secondKey)
        ->set("features.tr.{$secondKey}.description", 'Güncel ikinci açıklama')
        ->set("features.en.{$secondKey}.description", 'Updated second description')
        ->assertSet("features.tr.{$firstKey}.description", 'İlk açıklama')
        ->assertSet("features.en.{$firstKey}.description", 'First description')
        ->assertSet("features.tr.{$secondKey}.title", 'İkinci Özellik')
        ->assertSet("features.en.{$secondKey}.title", 'Second Feature');
});

it('keeps reordered existing image translations isolated', function () {
    $project = new PortfolioProject([
        'slug' => 'image-order-test',
        'status' => 'draft',
        'technologies' => [],
    ]);
    $project->setTranslations('title', ['tr' => 'Görsel Testi', 'en' => 'Image Test']);
    $project->save();

    $firstImage = $project->images()->create([
        'path' => 'images/portfolio/first.jpg',
        'sort_order' => 0,
    ]);
    $firstImage->setTranslations('title', ['tr' => 'Birinci', 'en' => 'First']);
    $firstImage->save();

    $secondImage = $project->images()->create([
        'path' => 'images/portfolio/second.jpg',
        'sort_order' => 1,
    ]);
    $secondImage->setTranslations('title', ['tr' => 'İkinci', 'en' => 'Second']);
    $secondImage->save();

    $component = Livewire::test(PortfolioEditor::class, ['project' => $project]);
    [$firstKey, $secondKey] = $component->get('existingImageOrder');

    $component
        ->assertSeeHtml('wire:key="existing-image-tr-'.$firstKey.'"')
        ->assertSeeHtml('wire:model="existingImages.'.$secondKey.'.translations.tr.title"')
        ->assertSeeHtml('wire:click="moveExistingImage(1, -1)"')
        ->call('moveExistingImage', 1, -1)
        ->assertSet('existingImageOrder.0', $secondKey)
        ->set("existingImages.{$secondKey}.translations.tr.title", 'Güncel İkinci')
        ->set("existingImages.{$secondKey}.translations.en.title", 'Updated Second')
        ->assertSet("existingImages.{$firstKey}.translations.tr.title", 'Birinci')
        ->assertSet("existingImages.{$firstKey}.translations.en.title", 'First')
        ->call('switchLang', 'en')
        ->assertSeeHtml('wire:key="existing-image-en-'.$secondKey.'"');
});

it('keeps upload metadata attached after removing an earlier upload', function () {
    $component = Livewire::test(PortfolioEditor::class)
        ->set('uploads', [
            UploadedFile::fake()->image('first.jpg'),
            UploadedFile::fake()->image('second.jpg'),
        ]);
    [$firstUploadKey, $secondUploadKey] = $component->get('uploadOrder');

    $component
        ->assertSeeHtml('wire:click="discardUpload(0)"')
        ->set("uploadTranslations.{$firstUploadKey}.tr.title", 'Birinci')
        ->set("uploadTranslations.{$secondUploadKey}.tr.title", 'İkinci')
        ->call('discardUpload', 0)
        ->assertSet('uploadOrder.0', $secondUploadKey)
        ->assertSet("uploadTranslations.{$secondUploadKey}.tr.title", 'İkinci');
});

it('persists portfolio repeater rows in their visible order without internal keys', function () {
    $component = Livewire::test(PortfolioEditor::class)
        ->set('slug', 'repeater-key-test')
        ->set('translations.tr.title', 'Repeater Testi')
        ->set('translations.en.title', 'Repeater Test')
        ->call('addItem', 'features')
        ->call('addItem', 'features');
    [$firstKey, $secondKey] = $component->get('repeaterOrder.features');

    $component
        ->set("features.tr.{$firstKey}.title", 'Birinci')
        ->set("features.en.{$firstKey}.title", 'First')
        ->set("features.tr.{$secondKey}.title", 'İkinci')
        ->set("features.en.{$secondKey}.title", 'Second')
        ->call('moveItem', 'features', $secondKey, -1)
        ->call('save')
        ->assertHasNoErrors();

    $project = PortfolioProject::where('slug', 'repeater-key-test')->firstOrFail();

    expect($project->getTranslation('features', 'tr')[0]['title'])->toBe('İkinci')
        ->and($project->getTranslation('features', 'tr')[0])->not->toHaveKey('_key');
});

it('reorders portfolio projects from the manager list', function () {
    $firstProject = new PortfolioProject([
        'slug' => 'first-sort-project',
        'status' => 'active',
        'project_date' => '2026-01-01',
        'technologies' => [],
        'sort_order' => 0,
    ]);
    $firstProject->setTranslations('title', ['tr' => 'Birinci Proje', 'en' => 'First Project']);
    $firstProject->save();

    $secondProject = new PortfolioProject([
        'slug' => 'second-sort-project',
        'status' => 'active',
        'project_date' => '2026-01-02',
        'technologies' => [],
        'sort_order' => 1,
    ]);
    $secondProject->setTranslations('title', ['tr' => 'İkinci Proje', 'en' => 'Second Project']);
    $secondProject->save();

    Livewire::test(PortfolioManager::class)
        ->call('moveProject', $secondProject->id, -1);

    expect($secondProject->fresh()->sort_order)->toBe(0)
        ->and($firstProject->fresh()->sort_order)->toBe(1);
});

it('updates and deletes projects with their uploaded files', function () {
    $directory = public_path('images/portfolio/test-project');
    File::ensureDirectoryExists($directory);
    File::put($directory.'/screen.jpg', 'test');

    $project = new PortfolioProject([
        'slug' => 'test-project',
        'status' => 'draft',
        'technologies' => [],
    ]);
    $project->setTranslation('title', 'tr', 'Eski Başlık');
    $project->setTranslation('title', 'en', 'Old Title');
    $project->save();

    $image = $project->images()->create([
        'path' => 'images/portfolio/test-project/screen.jpg',
        'sort_order' => 0,
    ]);

    Livewire::test(PortfolioEditor::class, ['project' => $project])
        ->set('translations.tr.title', 'Yeni Başlık')
        ->set('translations.en.title', 'New Title')
        ->call('save')
        ->assertHasNoErrors();

    expect($project->fresh()->getTranslation('title', 'tr'))->toBe('Yeni Başlık');

    Livewire::test(PortfolioManager::class)
        ->call('delete', $project->id);

    expect(PortfolioProject::find($project->id))->toBeNull()
        ->and(public_path($image->path))->not->toBeFile();
});
