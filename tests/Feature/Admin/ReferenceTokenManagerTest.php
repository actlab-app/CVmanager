<?php

use App\Livewire\Admin\ReferenceTokenManager;
use App\Models\ReferenceToken;
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
