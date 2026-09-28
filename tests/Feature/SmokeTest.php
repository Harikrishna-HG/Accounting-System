<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('root sends a guest straight to login', function () {
    // The root path is gated on dashboard.view, so a guest no longer takes a
    // two-hop redirect through the dashboard.
    $this->get('/')->assertRedirect('/login');
});

test('root sends a permitted user to the accounting dashboard', function () {
    (new RoleAndPermissionSeeder)->run();

    $user = User::factory()->create([
        'role_id' => Role::where('slug', 'accountant')->firstOrFail()->id,
    ]);

    $this->actingAs($user)->get('/')->assertRedirect('/accounting/dashboard');
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
