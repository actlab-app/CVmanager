<?php

use App\Livewire\Web\About;
use App\Models\AboutSetting;
use Livewire\Livewire;

it('renders the about page with its visual sections', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertSee('Karmaşık fikirleri')
        ->assertSee(asset('images/about/about-hero.png'))
        ->assertSeeHtml('class="about-hero-aurora"')
        ->assertSee('about-profile', false)
        ->assertSee('about-panel-enter', false)
        ->assertDontSee('x-on:pointermove="move($event)"', false)
        ->assertSee('prefers-reduced-motion', false);
});

it('switches the about page language', function () {
    Livewire::test(About::class)
        ->call('setLocale', 'en')
        ->assertSessionHas('locale', 'en');

    $this->withSession(['locale' => 'en'])
        ->get(route('about'))
        ->assertOk()
        ->assertSee('I turn complex ideas into calm digital products.');

    session(['locale' => 'en']);

    Livewire::test(About::class)
        ->call('setLocale', 'de')
        ->assertSessionHas('locale', 'en');
});

it('renders managed about content', function () {
    $setting = new AboutSetting([
        'profile_image_path' => 'images/about/custom-profile.png',
    ]);
    $setting->setTranslations('headline', [
        'tr' => 'Yönetilen hakkımda başlığı',
        'en' => 'Managed about headline',
    ]);
    $setting->setTranslations('hero_panels', [
        'tr' => [['image_path' => 'images/about/custom-hero.png', 'title' => 'Yönetilen panel', 'description' => 'Yönetilen açıklama']],
        'en' => [['image_path' => 'images/about/custom-hero.png', 'title' => 'Managed panel', 'description' => 'Managed description']],
    ]);
    $setting->save();

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('Yönetilen hakkımda başlığı')
        ->assertSee('Yönetilen panel')
        ->assertSee(asset('images/about/custom-hero.png'))
        ->assertSee(asset('images/about/custom-profile.png'));
});
