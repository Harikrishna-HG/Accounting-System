<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('forgot password page is reachable', function () {
    $this->get('/forgot-password')->assertStatus(200);
});

test('a reset link is emailed for a registered address', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertRedirect('/login');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('an unknown address gets the same response and no email', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'nobody@example.com'])
        ->assertRedirect('/login');

    Notification::assertNothingSent();
});

test('the reset form renders with the token and email', function () {
    $this->get('/reset-password/sometoken?email=user@example.com')
        ->assertStatus(200)
        ->assertSee('sometoken')
        ->assertSee('user@example.com');
});

test('a valid token stores the new password', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword@123',
        'password_confirmation' => 'NewPassword@123',
    ])->assertRedirect('/login');

    expect(Hash::check('NewPassword@123', $user->fresh()->password))->toBeTrue();
});

test('an invalid token is rejected', function () {
    $user = User::factory()->create();

    $this->post('/reset-password', [
        'token' => 'not-a-real-token',
        'email' => $user->email,
        'password' => 'NewPassword@123',
        'password_confirmation' => 'NewPassword@123',
    ])->assertSessionHasErrors('email');
});

test('the new password must be confirmed and at least eight characters', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'short',
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors(['password']);
});
