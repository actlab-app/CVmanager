<?php

use App\Livewire\Admin\Dashboard;
use App\Models\ReferenceToken;
use App\Models\ReferenceVisit;
use App\Models\SiteSetting;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/admin');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard')->assertStatus(200);
});

test('dashboard shows the top reference token visit chart', function () {
    $this->actingAs(User::factory()->create());

    foreach (range(1, 11) as $index) {
        $referenceToken = ReferenceToken::query()->create([
            'name' => 'Source '.$index,
            'token' => 'TOKEN'.$index,
            'visits_count' => 12 - $index,
        ]);

        foreach (range(1, 12 - $index) as $visitIndex) {
            ReferenceVisit::query()->create([
                'reference_token_id' => $referenceToken->id,
                'path' => 'cv',
                'landing_url' => route('cv', ['rt' => $referenceToken->token]),
                'ip_hash' => 'ip-'.$index,
                'user_agent_hash' => 'agent-'.$index,
                'visited_at' => now()->subMinutes($visitIndex),
            ]);
        }
    }

    $this->get('/dashboard')
        ->assertOk()
        ->assertSee('Referans Token Ziyaretleri')
        ->assertSeeInOrder(['Source 1', 'Source 2', 'Source 10'])
        ->assertDontSee('Source 11');
});

test('dashboard reference token chart can be filtered by date range', function () {
    $this->actingAs(User::factory()->create());

    $oldSource = ReferenceToken::query()->create([
        'name' => 'Old Source',
        'token' => 'OLDTOKEN',
    ]);

    $recentSource = ReferenceToken::query()->create([
        'name' => 'Recent Source',
        'token' => 'RECENTTOKEN',
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $oldSource->id,
        'path' => 'cv',
        'landing_url' => route('cv', ['rt' => $oldSource->token]),
        'ip_hash' => 'old-ip',
        'user_agent_hash' => 'old-agent',
        'visited_at' => now()->subDays(10),
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $recentSource->id,
        'path' => 'cv',
        'landing_url' => route('cv', ['rt' => $recentSource->token]),
        'ip_hash' => 'recent-ip',
        'user_agent_hash' => 'recent-agent',
        'visited_at' => now(),
    ]);

    Livewire::test(Dashboard::class)
        ->set('referenceTokenChartDateFrom', now()->subDay()->toDateString())
        ->assertSee('Recent Source')
        ->assertDontSee('Old Source');
});

test('dashboard persists the preferred cv theme', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Dashboard::class)
        ->call('selectPreferredCvTheme', 'classic')
        ->assertSet('preferredCvTheme', 'classic');

    expect(SiteSetting::first()?->preferred_cv_theme)->toBe('classic');
});
