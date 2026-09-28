<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seeded(): void
{
    (new RoleAndPermissionSeeder)->run();
}

function userWith(string $roleSlug, array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'role_id' => Role::where('slug', $roleSlug)->firstOrFail()->id,
    ], $attributes));
}

const ACCOUNTING_PATHS = [
    '/accounting/dashboard',
    '/accounting/products',
    '/accounting/clients',
    '/accounting/suppliers',
    '/accounting/invoices',
    '/accounting/payments',
    '/accounting/expenses',
    '/accounting/purchase-orders',
    '/accounting/transactions',
    '/accounting/reports',
];

// =========================================================================
// Access is driven by permissions, not by a hardcoded role allowlist.
// =========================================================================

it('lets an accountant open every accounting module', function (string $path) {
    seeded();

    $this->actingAs(userWith('accountant'))->get($path)->assertOk();
})->with(ACCOUNTING_PATHS);

it('keeps administrative pages away from an accountant', function (string $path) {
    seeded();

    $this->actingAs(userWith('accountant'))->get($path)->assertForbidden();
})->with(['/accounting/audit-logs', '/dashboard/users', '/dashboard/roles']);

it('lets an auditor read the audit log, reports and invoices only', function (string $path) {
    seeded();

    $this->actingAs(userWith('auditor'))->get($path)->assertOk();
})->with(['/accounting/dashboard', '/accounting/audit-logs', '/accounting/reports', '/accounting/invoices']);

it('denies an auditor the modules they have no permission for', function (string $path) {
    seeded();

    $this->actingAs(userWith('auditor'))->get($path)->assertForbidden();
})->with([
    '/accounting/products', '/accounting/clients', '/accounting/suppliers',
    '/accounting/payments', '/accounting/expenses', '/accounting/purchase-orders',
    '/dashboard/users', '/dashboard/roles',
]);

it('denies roles.manage to an admin because the role does not grant it', function () {
    seeded();

    $this->actingAs(userWith('admin'))->get('/dashboard/roles')->assertForbidden();
});

// =========================================================================
// Sign-in always lands somewhere the account can actually open.
// =========================================================================

it('sends a permitted account to the dashboard after login', function (string $roleSlug) {
    seeded();
    userWith($roleSlug, ['email' => $roleSlug.'@example.com']);

    $this->post('/login', [
        'email' => $roleSlug.'@example.com',
        'password' => 'password',
    ])->assertRedirect(route('accounting.dashboard'));

    $this->get('/accounting/dashboard')->assertOk();
})->with(['accountant', 'auditor', 'admin', 'super-admin']);

it('lands an account with no permissions on a no-access page instead of a 403', function () {
    seeded();
    userWith('user', ['email' => 'noperms@example.com']);

    $this->post('/login', [
        'email' => 'noperms@example.com',
        'password' => 'password',
    ])->assertRedirect(route('no-access'));

    $this->get(route('no-access'))
        ->assertOk()
        ->assertSee('लगआउट');
});

it('requires authentication to reach the no-access page', function () {
    seeded();

    $this->get('/no-access')->assertRedirect('/login');
});

// =========================================================================
// Public self-registration is closed.
// =========================================================================

it('no longer exposes a registration page', function () {
    seeded();

    $this->get('/register')->assertRedirect('/login');
    $this->post('/register', [
        'name' => 'Self Registered',
        'email' => 'self@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/login');

    expect(User::where('email', 'self@example.com')->exists())->toBeFalse();
});

it('does not advertise account creation on the login page', function () {
    seeded();

    $this->get('/login')
        ->assertOk()
        ->assertSee('Contact your administrator')
        ->assertDontSee('Create Account');
});

// =========================================================================
// The sidebar only offers links the signed-in role can open.
// =========================================================================

it('hides sidebar links an accountant cannot open', function () {
    seeded();

    $this->actingAs(userWith('accountant'))
        ->get('/accounting/dashboard')
        ->assertOk()
        // Anchored on the sidebar's own markup: the dashboard body itself
        // mentions products and payments, so a bare word check would pass
        // even when the link is still rendered.
        ->assertSee('<span class="menu-text">Invoices</span>', false)
        ->assertDontSee('<span class="menu-text">Audit Log</span>', false)
        ->assertDontSee('<span class="menu-text">Roles &amp; Permissions</span>', false);
});

it('shows sidebar links an auditor can open', function () {
    seeded();

    $this->actingAs(userWith('auditor'))
        ->get('/accounting/dashboard')
        ->assertOk()
        ->assertSee('<span class="menu-text">Audit Log</span>', false)
        ->assertSee('<span class="menu-text">Reports</span>', false)
        ->assertSee('<span class="menu-text">Invoices</span>', false)
        ->assertDontSee('<span class="menu-text">Products</span>', false)
        ->assertDontSee('<span class="menu-text">Payments</span>', false);
});

// =========================================================================
// A permission change takes effect without touching routes.
// =========================================================================

it('honours a permission revoked after the role was seeded', function () {
    seeded();

    $accountant = userWith('accountant');
    $this->actingAs($accountant)->get('/accounting/invoices')->assertOk();

    $role = Role::where('slug', 'accountant')->firstOrFail();
    $role->permissions()->detach(
        $role->permissions()->where('slug', 'invoices.manage')->pluck('permissions.id')
    );

    $this->actingAs($accountant->fresh())->get('/accounting/invoices')->assertForbidden();
});
