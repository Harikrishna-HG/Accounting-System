<?php

namespace App\Listeners;

use App\Support\AuditRecorder;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * Records every authentication attempt. Laravel already fires these three
 * events on every login, failed login and logout; previously nothing listened
 * to them, so authentication was completely untraceable.
 */
class LogAuthentication
{
    public function handleLogin(Login $event): void
    {
        AuditRecorder::record([
            'user_id' => $event->user->id,
            'event' => 'login.success',
            'auditable_type' => $event->user->getMorphClass(),
            'auditable_id' => $event->user->getKey(),
            'description' => 'Signed in: '.$event->user->email,
        ]);
    }

    public function handleLoginFailed(Failed $event): void
    {
        // The submitted email is recorded so repeated attempts against one
        // account are visible; the password is never touched.
        $email = $event->credentials['email'] ?? null;

        AuditRecorder::record([
            'user_id' => $event->user?->id,
            'event' => 'login.failed',
            'auditable_type' => $event->user?->getMorphClass(),
            'auditable_id' => $event->user?->getKey(),
            'description' => 'Failed sign-in attempt for '.($email ?: 'unknown account'),
            'new_values' => $email ? ['attempted_email' => $email] : null,
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        AuditRecorder::record([
            'user_id' => $user->id,
            'event' => 'login.logout',
            'auditable_type' => $user->getMorphClass(),
            'auditable_id' => $user->getKey(),
            'description' => 'Signed out: '.$user->email,
        ]);
    }
}
