<?php

use App\Livewire\Admin\AboutManager;
use App\Models\AboutSetting;
use App\Models\User;
use Database\Seeders\AboutSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

afterEach(function () {
    File::deleteDirectory(public_path('images/about/uploads'));
});

it('requires authentication for about management', function () {
    auth()->logout();

    $this->get(route('about-manager'))->assertRedirect('/admin');
});

it('keeps reordered about rows isolated across languages', function () {
    $component = Livewire::test(AboutManager::class);
    [$firstKey, $secondKey] = $component->get('repeaterOrder.focus_cards');

    $component
        ->assertSeeHtml('wire:model="focus_cards.tr.'.$firstKey.'.title"')
        ->set("focus_cards.tr.{$firstKey}.title", 'Birinci')
        ->set("focus_cards.en.{$firstKey}.title", 'First')
        ->set("focus_cards.tr.{$secondKey}.title", 'İkinci')
        ->set("focus_cards.en.{$secondKey}.title", 'Second')
        ->call('moveItem', 'focus_cards', $secondKey, -1)
        ->assertSet('repeaterOrder.focus_cards.0', $secondKey)
        ->set("focus_cards.tr.{$secondKey}.text", 'Güncel ikinci')
        ->set("focus_cards.en.{$secondKey}.text", 'Updated second')
        ->assertSet("focus_cards.tr.{$firstKey}.title", 'Birinci')
        ->assertSet("focus_cards.en.{$firstKey}.title", 'First');
});

it('saves multilingual about content and uploads hero showcase images to public', function () {
    $component = Livewire::test(AboutManager::class);

    $component
        ->set('translations.tr.headline', 'Yeni hakkımda başlığı')
        ->set('translations.en.headline', 'New about headline')
        ->set('hero_showcases.tr.0.title', 'Yeni hero başlığı')
        ->set('hero_showcases.en.0.title', 'New hero title')
        ->set('hero_showcases.tr.0.description', 'Yeni hero açıklaması')
        ->set('hero_showcases.en.0.description', 'New hero description')
        ->set('heroShowcaseUploads.0', UploadedFile::fake()->image('about-wide.jpg', 1600, 900))
        ->set('profileImage', UploadedFile::fake()->image('profile.jpg', 600, 600))
        ->call('save')
        ->assertHasNoErrors();

    $setting = AboutSetting::firstOrFail();
    $heroPanel = $setting->getTranslation('hero_panels', 'tr')[0];

    expect($setting->getTranslation('headline', 'tr'))->toBe('Yeni hakkımda başlığı')
        ->and($heroPanel['title'])->toBe('Yeni hero başlığı')
        ->and($heroPanel['description'])->toBe('Yeni hero açıklaması')
        ->and($heroPanel['image_path'])->toStartWith('images/about/uploads/')
        ->and(public_path($heroPanel['image_path']))->toBeFile();
    expect($setting->profile_image_path)->toStartWith('images/about/uploads/')
        ->and(public_path($setting->profile_image_path))->toBeFile();
});

it('imports the complete demo about content idempotently', function () {
    $this->seed(AboutSeeder::class);
    $this->seed(AboutSeeder::class);

    $setting = AboutSetting::firstOrFail();

    expect(AboutSetting::count())->toBe(1)
        ->and($setting->getTranslation('headline', 'tr'))->toBe(config('about.translations.tr.headline'))
        ->and($setting->getTranslation('hero_panels', 'en'))->toBe(config('about.hero_panels.en'))
        ->and($setting->getTranslation('focus_cards', 'tr'))->toBe(config('about.focus_cards.tr'))
        ->and($setting->profile_image_path)->toBeNull();
});
