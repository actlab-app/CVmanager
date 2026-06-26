<?php

use App\Livewire\Web\Cv;
use App\Models\ContactItem;
use App\Models\CvRecord;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('returns not found when no CV record exists', function () {
    $this->get(route('cv'))->assertNotFound();
});

it('keeps the full portfolio href but displays a clean host in the classic CV', function () {
    SiteSetting::query()->create([
        'preferred_cv_theme' => 'classic',
        'web_theme_color' => 'rose',
    ]);

    $record = new CvRecord([
        'full_name' => 'Jane Doe',
        'qr_url' => 'https://cvm.actlab.app?rt=343343',
    ]);
    $record->setTranslation('job_title', 'tr', 'Full-Stack Developer');
    $record->save();

    $this->withSession(['locale' => 'tr'])
        ->get(route('cv'))
        ->assertOk()
        ->assertSee('href="https://cvm.actlab.app?rt=343343"', false)
        ->assertSeeText('Portfolyo: cvm.actlab.app')
        ->assertDontSeeText('Portfolyo: https://cvm.actlab.app?rt=343343');
});

it('redirects legacy public CV urls to the canonical CV route', function () {
    $this->get('/')->assertRedirect('/cv');
    $this->get('/cv/modern')->assertRedirect('/cv');
});

it('does not expose the removed Harvard CV route', function () {
    $this->get('/cv/harvard')->assertNotFound();
});

it('renders normalized CV data for the active locale', function () {
    $record = new CvRecord([
        'full_name' => 'Jane Doe',
        'qr_url' => 'https://example.com/portfolio',
    ]);
    $record->setTranslation('job_title', 'tr', 'YazÄ±lÄ±m GeliÅŸtirici');
    $record->setTranslation('quick_infos', 'tr', [['icon' => 'map-pin', 'title' => 'Konum', 'value' => 'Ä°stanbul']]);
    $record->save();

    $this->withSession(['locale' => 'tr'])
        ->get(route('cv'))
        ->assertOk()
        ->assertSee('Jane Doe')
        ->assertSee('YazÄ±lÄ±m GeliÅŸtirici')
        ->assertSee('Ä°stanbul')
        ->assertSee('https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=', false);
});

it('only accepts supported locales', function () {
    $record = new CvRecord(['full_name' => 'Jane Doe']);
    $record->save();

    Livewire::withQueryParams([])
        ->test(Cv::class)
        ->call('setLocale', 'de')
        ->assertSessionMissing('locale')
        ->call('setLocale', 'tr')
        ->assertSessionHas('locale', 'tr');
});

it('renders the classic CV theme when selected from site settings', function () {
    SiteSetting::query()->create([
        'preferred_cv_theme' => 'classic',
        'web_theme_color' => 'rose',
    ]);

    $record = new CvRecord(['full_name' => 'Jane Doe']);
    $record->setTranslation('job_title', 'tr', 'Full-Stack Developer');
    $record->setTranslation('classic_profile_summary', 'tr', 'ATS uyumlu klasik CV Ã¶zeti.');
    $record->setTranslation('experiences', 'tr', [
        [
            'icon' => 'briefcase-business',
            'company' => 'Calisto Digital',
            'description' => 'Full-stack developer',
            'detailed_description' => 'Olculebilir proje teslimati.',
        ],
    ]);
    $record->save();

    $this->withSession(['locale' => 'tr'])
        ->get(route('cv'))
        ->assertOk()
        ->assertSee('Profesyonel Özet')
        ->assertSee('ATS uyumlu klasik CV Ã¶zeti.')
        ->assertSee('Calisto Digital')
        ->assertSee('Olculebilir proje teslimati.')
        ->assertSee('--color-accent: #E11D48', false)
        ->assertSee('--classic-blue: var(--color-accentDark)', false)
        ->assertDontSee('--classic-blue: #1d4ed8', false);
});

it('renders labelled contact rows and location in the classic CV header', function () {
    SiteSetting::query()->create(['preferred_cv_theme' => 'classic']);

    ContactItem::query()->create([
        'label' => ['tr' => 'Telefon', 'en' => 'Phone'],
        'value' => '+90 555 000 00 00',
        'url' => 'tel:+905550000000',
        'icon' => 'phone',
        'is_active' => true,
        'show_in_cv' => true,
        'sort_order' => 1,
    ]);

    $record = new CvRecord(['full_name' => 'Jane Doe']);
    $record->setTranslation('job_title', 'tr', 'Full-Stack Developer');
    $record->setTranslation('quick_infos', 'tr', [
        ['icon' => 'map-pin', 'title' => 'Konum', 'value' => 'İstanbul / Türkiye'],
        ['icon' => 'clock', 'title' => 'Uygunluk', 'value' => 'Proje bazlı'],
    ]);
    $record->save();

    $this->withSession(['locale' => 'tr'])
        ->get(route('cv'))
        ->assertOk()
        ->assertSee('Telefon:')
        ->assertSee('+90 555 000 00 00')
        ->assertSee('Konum:')
        ->assertSee('İstanbul / Türkiye');
});
