<?php

use App\Livewire\Admin\ReferenceTokenManager;
use App\Models\ReferenceToken;
use App\Models\ReferenceVisit;
use App\Models\User;
use Livewire\Livewire;

it('requires authentication for reference token management', function () {
    $this->get(route('reference-token-manager.index'))->assertRedirect('/admin');
});

it('renders the reference token manager', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('reference-token-manager.index'))
        ->assertOk()
        ->assertSee('Referans Tokenleri');
});

it('creates reference tokens from the manager', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ReferenceTokenManager::class)
        ->set('name', 'Microsoft')
        ->set('token', 'MICROSOFT1')
        ->set('description', 'Microsoft başvuru linki')
        ->call('save')
        ->assertHasNoErrors();

    $referenceToken = ReferenceToken::query()->firstOrFail();

    expect($referenceToken->name)->toBe('Microsoft')
        ->and($referenceToken->token)->toBe('MICROSOFT1')
        ->and($referenceToken->is_active)->toBeTrue();
});

it('shows token detail analytics in a single modal', function () {
    $this->actingAs(User::factory()->create());

    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $referenceToken->id,
        'path' => 'cv',
        'landing_url' => route('cv', ['rt' => $referenceToken->token]),
        'ip_hash' => 'ip-a',
        'user_agent_hash' => 'agent-a',
        'visited_at' => now(),
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $referenceToken->id,
        'path' => 'contact',
        'landing_url' => route('contact', ['rt' => $referenceToken->token]),
        'ip_hash' => 'ip-b',
        'user_agent_hash' => 'agent-a',
        'visited_at' => now(),
    ]);

    Livewire::test(ReferenceTokenManager::class)
        ->call('showDetail', $referenceToken->id)
        ->assertSet('showDetailModal', true)
        ->assertSee('Ziyaret Analitiği')
        ->assertSee("A IP'si")
        ->assertSee('A User Agent')
        ->assertSee('İletişim')
        ->assertSee(route('cv', ['rt' => $referenceToken->token]));
});

it('filters token detail analytics by date range', function () {
    $this->actingAs(User::factory()->create());

    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $referenceToken->id,
        'path' => 'cv',
        'landing_url' => route('cv', ['rt' => $referenceToken->token]),
        'ip_hash' => 'old-ip',
        'user_agent_hash' => 'old-agent',
        'visited_at' => now()->subDays(5),
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $referenceToken->id,
        'path' => 'contact',
        'landing_url' => route('contact', ['rt' => $referenceToken->token]),
        'ip_hash' => 'recent-ip',
        'user_agent_hash' => 'recent-agent',
        'visited_at' => now(),
    ]);

    Livewire::test(ReferenceTokenManager::class)
        ->call('showDetail', $referenceToken->id)
        ->set('detailDateFrom', now()->subDay()->toDateString())
        ->assertSee('1 sayfa ziyareti')
        ->assertSee('İletişim')
        ->assertDontSee('2 sayfa ziyareti');
});

it('marks visitor hashes that match the current admin request', function () {
    $this->actingAs(User::factory()->create());
    $this->withServerVariables(['HTTP_USER_AGENT' => 'Admin Browser']);

    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $referenceToken->id,
        'path' => 'cv',
        'landing_url' => route('cv', ['rt' => $referenceToken->token]),
        'ip_hash' => hash_hmac('sha256', '127.0.0.1', (string) config('app.key')),
        'user_agent_hash' => hash_hmac('sha256', 'Admin Browser', (string) config('app.key')),
        'visited_at' => now(),
    ]);

    Livewire::test(ReferenceTokenManager::class)
        ->call('showDetail', $referenceToken->id)
        ->assertSee('Ziyaretçi Temizleme')
        ->assertSee('(Sizin IP)');
});

it('cleans all visits for a selected visitor on the selected token', function () {
    $this->actingAs(User::factory()->create());

    $referenceToken = ReferenceToken::query()->create([
        'name' => 'Microsoft',
        'token' => 'MICROSOFT1',
        'visits_count' => 3,
        'last_visited_at' => now(),
    ]);

    $otherToken = ReferenceToken::query()->create([
        'name' => 'Google',
        'token' => 'GOOGLE1',
        'visits_count' => 1,
        'last_visited_at' => now(),
    ]);

    foreach (range(1, 2) as $index) {
        ReferenceVisit::query()->create([
            'reference_token_id' => $referenceToken->id,
            'path' => 'cv',
            'landing_url' => route('cv', ['rt' => $referenceToken->token]),
            'ip_hash' => 'visitor-ip',
            'user_agent_hash' => 'visitor-agent',
            'visited_at' => now()->subMinutes($index),
        ]);
    }

    ReferenceVisit::query()->create([
        'reference_token_id' => $referenceToken->id,
        'path' => 'contact',
        'landing_url' => route('contact', ['rt' => $referenceToken->token]),
        'ip_hash' => 'other-ip',
        'user_agent_hash' => 'other-agent',
        'visited_at' => now(),
    ]);

    ReferenceVisit::query()->create([
        'reference_token_id' => $otherToken->id,
        'path' => 'cv',
        'landing_url' => route('cv', ['rt' => $otherToken->token]),
        'ip_hash' => 'visitor-ip',
        'user_agent_hash' => 'visitor-agent',
        'visited_at' => now(),
    ]);

    Livewire::test(ReferenceTokenManager::class)
        ->call('showDetail', $referenceToken->id)
        ->assertSee('Ziyaretçi Temizleme')
        ->assertSee('visitor-ip')
        ->assertSeeHtml('wire:confirm="Bu ziyaretçinin bu tokene yaptığı tüm ziyaretleri temizlemek istiyor musunuz?"')
        ->call('deleteVisitorVisits', rtrim(strtr(base64_encode(json_encode(['visitor-ip', 'visitor-agent'])), '+/', '-_'), '='));

    expect(ReferenceVisit::query()->where('reference_token_id', $referenceToken->id)->count())->toBe(1)
        ->and(ReferenceVisit::query()->where('reference_token_id', $referenceToken->id)->where('ip_hash', 'visitor-ip')->count())->toBe(0)
        ->and(ReferenceVisit::query()->where('reference_token_id', $otherToken->id)->where('ip_hash', 'visitor-ip')->count())->toBe(1)
        ->and($referenceToken->refresh()->visits_count)->toBe(1)
        ->and($referenceToken->last_visited_at)->not->toBeNull();
});
