<?php

test('root redirects to the accounting dashboard', function () {
    $this->get('/')->assertRedirect('/accounting/dashboard');
});

test('login page is reachable', function () {
    $this->get('/login')->assertStatus(200);
});

test('protected pages redirect unauthenticated users to login', function () {
    $this->get('/accounting/dashboard')->assertRedirect('/login');
});

test('language switch rejects unsupported locales', function () {
    $this->get('/language/xx')->assertStatus(404);
});

test('language switch stores a supported locale in the session', function () {
    $this->from('/login')
        ->get('/language/ne')
        ->assertRedirect('/login');

    expect(session('locale'))->toBe('ne');
});
