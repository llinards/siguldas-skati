<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/lv/login');

    $response->assertStatus(200);
});


test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/lv/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/lv/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/lv/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
