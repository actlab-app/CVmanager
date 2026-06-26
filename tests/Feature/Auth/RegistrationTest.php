<?php

use Illuminate\Support\Facades\Route;

test('registration routes are disabled', function () {
    expect(Route::has('register'))->toBeFalse()
        ->and(Route::has('register.store'))->toBeFalse();

    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
});

test('login screen does not show the sign up link', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('Sign up')
        ->assertDontSee("Don't have an account?");
});
