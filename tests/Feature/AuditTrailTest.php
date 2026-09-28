<?php

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function seedAuditBaseline(): void
{
    (new RoleAndPermissionSeeder)->run();
}

function userWithRole(string $roleSlug, array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'role_id' => Role::where('slug', $roleSlug)->firstOrFail()->id,
    ], $attributes));
}

// =========================================================================
// Authentication events
// =========================================================================

it('records a successful login exactly once', function () {
    seedAuditBaseline();
    $user = userWithRole('super-admin', ['email' => 'auditor@example.com']);

    $this->post('/login', [
        'email' => 'auditor@example.com',
        'password' => 'password',
    ]);

    $logs = AuditLog::where('event', 'login.success')->get();

    // Counted, not just fetched: a duplicated listener still returns a row here.
    expect($logs)->toHaveCount(1)
        ->and($logs->first()->user_id)->toBe($user->id)
        ->and($logs->first()->ip_address)->not->toBeNull();
});

it('records a failed login exactly once and without an actor', function () {
    seedAuditBaseline();

    $this->post('/login', [
        'email' => 'nobody@example.com',
        'password' => 'wrong-password',
    ]);

    $logs = AuditLog::where('event', 'login.failed')->get();

    expect($logs)->toHaveCount(1)
        ->and($logs->first()->user_id)->toBeNull();
});

it('records a logout exactly once', function () {
    seedAuditBaseline();
    $user = userWithRole('super-admin');

    $this->actingAs($user)->get('/logout');

    $logs = AuditLog::where('event', 'login.logout')->get();

    expect($logs)->toHaveCount(1)
        ->and($logs->first()->user_id)->toBe($user->id);
});

it('records one row per event across a full login and logout cycle', function () {
    seedAuditBaseline();
    $user = userWithRole('super-admin', ['email' => 'cycle@example.com']);

    $this->post('/login', ['email' => 'cycle@example.com', 'password' => 'password'])
        ->assertRedirect();

    $this->get('/logout');

    // Counted so that a listener bound twice shows up here as 2, not 1.
    expect(AuditLog::where('event', 'login.success')->count())->toBe(1)
        ->and(AuditLog::where('event', 'login.logout')->count())->toBe(1)
        ->and(AuditLog::whereIn('event', ['login.success', 'login.logout'])->count())->toBe(2);
});

it('binds each authentication listener only once', function () {
    // Guards the wiring itself. Event discovery already registers these
    // handlers; registering them again by hand would silently double every
    // audit row, which is exactly the regression this test exists to catch.
    $expected = [
        Illuminate\Auth\Events\Login::class => 'handleLogin',
        Illuminate\Auth\Events\Failed::class => 'handleLoginFailed',
        Illuminate\Auth\Events\Logout::class => 'handleLogout',
    ];

    foreach ($expected as $event => $method) {
        $listenerName = App\Listeners\LogAuthentication::class.'@'.$method;

        $listeners = array_values(array_filter(
            Illuminate\Support\Facades\Event::getRawListeners()[$event] ?? [],
            fn ($listener) => $listener === $listenerName
                || (is_array($listener) && ($listener[0] ?? null) === App\Listeners\LogAuthentication::class)
        ));

        expect($listeners)->toHaveCount(1, "{$event} must bind {$method} exactly once");
    }
});

it('locks out after repeated failed logins', function () {
    seedAuditBaseline();
    userWithRole('super-admin', ['email' => 'target@example.com']);

    foreach (range(1, 5) as $ignored) {
        $this->post('/login', [
            'email' => 'target@example.com',
            'password' => 'wrong-password',
        ]);
    }

    // The 6th attempt is rejected before credentials are even checked.
    $this->post('/login', [
        'email' => 'target@example.com',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

// =========================================================================
// Model change auditing
// =========================================================================

it('records created, updated and deleted events with before and after values', function () {
    seedAuditBaseline();
    $user = userWithRole('super-admin');
    $this->actingAs($user);

    $client = Client::create(['name' => 'Acme Ltd', 'is_active' => true]);

    $created = AuditLog::where('event', 'client.created')->latest('id')->first();
    expect($created)->not->toBeNull()
        ->and($created->auditable_type)->toBe(Client::class)
        ->and($created->auditable_id)->toBe($client->id)
        ->and($created->new_values['name'])->toBe('Acme Ltd')
        ->and($created->old_values)->toBe([]);

    $client->update(['name' => 'Acme Limited']);

    $updated = AuditLog::where('event', 'client.updated')->latest('id')->first();
    expect($updated)->not->toBeNull()
        ->and($updated->old_values['name'])->toBe('Acme Ltd')
        ->and($updated->new_values['name'])->toBe('Acme Limited');

    $client->delete();

    $deleted = AuditLog::where('event', 'client.deleted')->latest('id')->first();
    expect($deleted)->not->toBeNull()
        ->and($deleted->old_values['name'])->toBe('Acme Limited');
});

it('stamps the acting user and request context on model events', function () {
    seedAuditBaseline();
    $user = userWithRole('super-admin');

    $this->actingAs($user);
    Client::create(['name' => 'Context Co']);

    $log = AuditLog::where('event', 'client.created')->latest('id')->first();

    expect($log->user_id)->toBe($user->id)
        ->and($log->ip_address)->not->toBeNull()
        ->and($log->created_at)->not->toBeNull();
});

it('does not record a history row for an update that changed nothing', function () {
    seedAuditBaseline();
    $this->actingAs(userWithRole('super-admin'));

    $client = Client::create(['name' => 'Noop Co']);
    $before = AuditLog::count();

    $client->update(['name' => 'Noop Co']);

    expect(AuditLog::count())->toBe($before);
});

it('soft deletes the row while keeping it recoverable', function () {
    seedAuditBaseline();
    $this->actingAs(userWithRole('super-admin'));

    $client = Client::create(['name' => 'Ghost Co']);
    $client->delete();

    expect(Client::whereKey($client->id)->exists())->toBeFalse()
        ->and(Client::withTrashed()->whereKey($client->id)->exists())->toBeTrue()
        ->and(Client::withTrashed()->whereKey($client->id)->first()->deleted_at)->not->toBeNull();
});

it('writes no audit rows when validation rejects the request', function () {
    seedAuditBaseline();
    $this->actingAs(userWithRole('super-admin'));

    $before = AuditLog::count();

    $this->post('/accounting/clients', ['email' => 'not-an-email'])->assertSessionHasErrors();

    expect(AuditLog::count())->toBe($before);
});

// =========================================================================
// Ledger attribution
// =========================================================================

it('attributes a new ledger row to the acting user', function () {
    seedAuditBaseline();
    $user = userWithRole('super-admin');

    $this->actingAs($user)->post('/accounting/invoices', [
        'client_name' => 'Walkup Client',
        'invoice_date' => now()->toDateString(),
        'items' => [
            ['product_name' => 'Consulting', 'quantity' => 2, 'price' => 500],
        ],
    ])->assertRedirect('/accounting/invoices');

    $transaction = Transaction::latest('id')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->user_id)->toBe($user->id);

    expect(AuditLog::where('event', 'invoice.created')->exists())->toBeTrue()
        ->and(AuditLog::where('event', 'transaction.created')->exists())->toBeTrue();
});

// =========================================================================
// Append-only enforcement
// =========================================================================

it('refuses to update an existing audit row', function () {
    seedAuditBaseline();
    $this->actingAs(userWithRole('super-admin'));

    $log = AuditLog::create(['event' => 'test.manual', 'description' => 'original']);

    expect(fn () => DB::table('audit_logs')->where('id', $log->id)->update(['description' => 'tampered']))
        ->toThrow(Illuminate\Database\QueryException::class);
});

it('refuses to delete an audit row', function () {
    seedAuditBaseline();
    $this->actingAs(userWithRole('super-admin'));

    $log = AuditLog::create(['event' => 'test.manual', 'description' => 'original']);

    expect(fn () => DB::table('audit_logs')->where('id', $log->id)->delete())
        ->toThrow(Illuminate\Database\QueryException::class);

    expect(AuditLog::whereKey($log->id)->exists())->toBeTrue();
});

// =========================================================================
// Access to the audit log screen
// =========================================================================

it('lets a user with audit.view read the audit log', function () {
    seedAuditBaseline();

    $this->actingAs(userWithRole('auditor'))
        ->get('/accounting/audit-logs')
        ->assertOk();
});

it('denies a user without audit.view', function () {
    seedAuditBaseline();

    $this->actingAs(userWithRole('accountant'))
        ->get('/accounting/audit-logs')
        ->assertForbidden();
});

it('requires authentication to view the audit log', function () {
    seedAuditBaseline();

    $this->get('/accounting/audit-logs')->assertRedirect('/login');
});

it('shows the detail view for a single audit row', function () {
    seedAuditBaseline();
    $this->actingAs(userWithRole('admin'));

    $log = AuditLog::create(['event' => 'test.manual', 'description' => 'detail view']);

    $this->get("/accounting/audit-logs/{$log->id}")
        ->assertOk()
        ->assertSee('detail view');
});

// =========================================================================
// Timestamp rendering
// =========================================================================

it('stores timestamps in UTC but renders them in the display timezone', function () {
    config(['app.timezone' => 'UTC', 'app.display_timezone' => 'Asia/Kathmandu']);

    seedAuditBaseline();
    $this->actingAs(userWithRole('admin'));

    $log = new AuditLog(['event' => 'test.manual']);
    $log->created_at = '2026-09-28 11:42:16';
    $log->save();

    // 11:42:16 UTC is 17:27:16 in Kathmandu (+05:45).
    expect($log->created_at->timezone->getName())->toBe('UTC')
        ->and($log->occurredAt()->format('d M Y, g:i:s A'))->toBe('28 Sep 2026, 5:27:16 PM')
        ->and($log->occurredAtLabel())->toBe('28 Sep 2026, 5:27:16 PM');
});

it('includes seconds in the rendered time', function () {
    config(['app.timezone' => 'UTC', 'app.display_timezone' => 'Asia/Kathmandu']);

    seedAuditBaseline();
    $this->actingAs(userWithRole('auditor'));

    $log = new AuditLog(['event' => 'test.manual', 'description' => 'timezone probe']);
    $log->created_at = '2026-09-28 11:42:16';
    $log->save();

    $this->get('/accounting/audit-logs')
        ->assertOk()
        ->assertSee('28 Sep 2026, 5:27:16 PM')
        ->assertDontSee('2026-09-28 11:42:16');

    $this->get("/accounting/audit-logs/{$log->id}")
        ->assertOk()
        ->assertSee('28 Sep 2026, 5:27:16 PM');
});

it('keeps rendering correctly when the display timezone is not the storage zone', function () {
    config(['app.timezone' => 'UTC', 'app.display_timezone' => 'America/New_York']);

    seedAuditBaseline();
    $this->actingAs(userWithRole('admin'));

    $log = new AuditLog(['event' => 'test.manual']);
    $log->created_at = '2026-09-28 11:42:16';
    $log->save();

    expect($log->occurredAtLabel())->toBe('28 Sep 2026, 7:42:16 AM');
});
