<?php

use App\Models\CvRecord;
use App\Models\ReferenceToken;
use App\Models\ReferenceVisit;
use App\Models\SiteSetting;

function createCvForReferenceTokenTests(?string $qrUrl = null): CvRecord
{
    $record = new CvRecord([
        'full_name' => 'Jane Doe',
        'qr_url' => $qrUrl,
    ]);

    $record->setTranslation('job_title', 'tr', 'Software Developer');
    $record->save();

    return $record;
}

it('records a visit for a valid reference token on a full public page load', function () {
    createCvForReferenceTokenTests();
    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    $this->get(route('cv', ['rt' => $referenceToken->token]))
        ->assertOk();

    expect($referenceToken->refresh()->visits_count)->toBe(1)
        ->and($referenceToken->last_visited_at)->not->toBeNull()
        ->and(ReferenceVisit::query()->count())->toBe(1)
        ->and(ReferenceVisit::query()->first()->path)->toBe('cv');
});

it('does not record visits for Livewire navigate requests', function () {
    createCvForReferenceTokenTests();
    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    $this->withHeader('X-Livewire-Navigate', 'true')
        ->get(route('cv', ['rt' => $referenceToken->token]))
        ->assertOk();

    expect($referenceToken->refresh()->visits_count)->toBe(0)
        ->and(ReferenceVisit::query()->count())->toBe(0);
});

it('allows visitors without tokens when strict reference mode is disabled', function () {
    createCvForReferenceTokenTests();

    $this->get(route('cv'))->assertOk();

    expect(ReferenceVisit::query()->count())->toBe(0);
});

it('redirects visitors without tokens when strict reference mode is enabled', function () {
    SiteSetting::query()->create([
        'block_visitors_without_reference_token' => true,
    ]);

    $this->get(route('cv'))
        ->assertRedirect(route('reference-token.required'));
});

it('redirects invalid tokens when strict reference mode is enabled', function () {
    SiteSetting::query()->create([
        'block_visitors_without_reference_token' => true,
    ]);

    $this->get(route('cv', ['rt' => 'UNKNOWN1']))
        ->assertRedirect(route('reference-token.required'));
});

it('carries the rt parameter in public navigation links and CV QR targets', function () {
    createCvForReferenceTokenTests(route('portfolio.index'));
    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    $this->get(route('cv', ['rt' => $referenceToken->token]))
        ->assertOk()
        ->assertSee(route('about', ['rt' => $referenceToken->token]), false)
        ->assertSee(route('portfolio.index', ['rt' => $referenceToken->token]), false)
        ->assertSee('https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=', false)
        ->assertSee('rt%3DMICROSOFT1', false);
});

it('does not double count redirect-only public routes', function () {
    createCvForReferenceTokenTests();
    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    $this->get(route('home', ['rt' => $referenceToken->token]))
        ->assertRedirect('/cv?rt=MICROSOFT1');

    expect($referenceToken->refresh()->visits_count)->toBe(0);
});

it('binds configured qr_token to both qr_url and second page portfolio links on public CV page', function () {
    createCvForReferenceTokenTests();
    SiteSetting::query()->create(['preferred_cv_theme' => 'classic']);

    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    $record = \App\Models\CvRecord::first();
    $record->update([
        'qr_page' => 'portfolio',
        'qr_token' => $referenceToken->token,
    ]);

    $project = new \App\Models\PortfolioProject([
        'slug' => 'test-project',
        'status' => 'active',
        'is_published' => true,
        'project_date' => '2026-06-13',
    ]);
    $project->setTranslation('title', 'tr', 'Test Project');
    $project->setTranslation('short_description', 'tr', 'Test Summary');
    $project->save();

    $this->get(route('cv'))
        ->assertOk()
        ->assertSee('rt=MICROSOFT1', false)
        ->assertSee(route('portfolio.show', ['project' => $project, 'rt' => $referenceToken->token]), false);
});

it('redirects without token when qr_token is empty or removed on CvRecord', function () {
    createCvForReferenceTokenTests();
    SiteSetting::query()->create(['preferred_cv_theme' => 'classic']);

    $record = \App\Models\CvRecord::first();
    $record->update([
        'qr_page' => 'portfolio',
        'qr_token' => null,
    ]);

    $project = new \App\Models\PortfolioProject([
        'slug' => 'test-project',
        'status' => 'active',
        'is_published' => true,
        'project_date' => '2026-06-13',
    ]);
    $project->setTranslation('title', 'tr', 'Test Project');
    $project->setTranslation('short_description', 'tr', 'Test Summary');
    $project->save();

    $this->get(route('cv'))
        ->assertOk()
        ->assertSee(rtrim(config('app.url', url('/')), '/') . '/portfolio', false)
        ->assertDontSee('rt=')
        ->assertSee(route('portfolio.show', $project), false);
});
