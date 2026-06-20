<?php

use App\Models\ReferenceToken;
use App\Models\User;

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
        ReferenceToken::query()->create([
            'name' => 'Source '.$index,
            'token' => 'TOKEN'.$index,
            'visits_count' => 12 - $index,
        ]);
    }

    $this->get('/dashboard')
        ->assertOk()
        ->assertSee('Referans Token Ziyaretleri')
        ->assertSeeInOrder(['Source 1', 'Source 2', 'Source 10'])
        ->assertDontSee('Source 11');
});
