<?php

use App\Livewire\Web\Cv;
use App\Models\CvRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('returns not found when no CV record exists', function () {
    $this->get(route('cv'))->assertNotFound();
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
    $record->setTranslation('job_title', 'tr', 'Yazılım Geliştirici');
    $record->setTranslation('quick_infos', 'tr', [['icon' => 'map-pin', 'title' => 'Konum', 'value' => 'İstanbul']]);
    $record->save();

    $this->withSession(['locale' => 'tr'])
        ->get(route('cv'))
        ->assertOk()
        ->assertSee('Jane Doe')
        ->assertSee('Yazılım Geliştirici')
        ->assertSee('İstanbul')
        ->assertSee(asset('images/portfolio-qr.png'));
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
