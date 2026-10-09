<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function balanceAdmin(): User
{
    (new \Database\Seeders\RoleAndPermissionSeeder)->run();

    return User::factory()->create([
        'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
    ]);
}

function balanceClient(string $name): Client
{
    return Client::create([
        'name' => $name,
        'is_active' => true,
        'total_purchases' => 0,
        'balance' => 0,
    ]);
}

function balancePostInvoice($test, User $user, Client $client, float $price = 200, int $quantity = 1): Invoice
{
    $test->actingAs($user)->post('/accounting/invoices', [
        'client_id' => $client->id,
        'client_name' => $client->name,
        'invoice_date' => now()->toDateString(),
        'discount' => 0,
        'tax_type' => 'none',
        'items' => [
            ['product_name' => 'Widget', 'quantity' => $quantity, 'price' => $price],
        ],
    ])->assertRedirect(route('accounting.invoices.index'));

    return Invoice::latest('id')->firstOrFail();
}

it('adds the invoice total to the client then reverses it on delete', function () {
    $user = balanceAdmin();
    $client = balanceClient('Acme');

    $invoice = balancePostInvoice($this, $user, $client);

    $client->refresh();
    expect((float) $client->total_purchases)->toBe(200.0)
        ->and((float) $client->balance)->toBe(200.0);

    $this->actingAs($user)
        ->delete("/accounting/invoices/{$invoice->id}")
        ->assertRedirect(route('accounting.invoices.index'));

    $client->refresh();
    expect((float) $client->total_purchases)->toBe(0.0)
        ->and((float) $client->balance)->toBe(0.0);
});

it('refuses to delete an invoice that has payments and keeps it', function () {
    $user = balanceAdmin();
    $client = balanceClient('Acme');

    $invoice = balancePostInvoice($this, $user, $client);

    $this->actingAs($user)->post('/accounting/payments', [
        'invoice_id' => $invoice->id,
        'client_id' => $client->id,
        'amount' => 50,
        'payment_date' => now()->toDateString(),
    ])->assertRedirect(route('accounting.payments.index'));

    $response = $this->actingAs($user)->delete("/accounting/invoices/{$invoice->id}");

    $response->assertRedirect(route('accounting.invoices.index'))
        ->assertSessionHas('error');

    expect(Invoice::whereKey($invoice->id)->exists())->toBeTrue();

    // The client still owes the unpaid remainder.
    expect((float) $client->fresh()->balance)->toBe(150.0);
});

it('moves an unpaid invoice balance when its client is reassigned', function () {
    $user = balanceAdmin();
    $from = balanceClient('From');
    $to = balanceClient('To');

    $invoice = balancePostInvoice($this, $user, $from);

    $this->actingAs($user)->put("/accounting/invoices/{$invoice->id}", [
        'client_id' => $to->id,
        'client_name' => $to->name,
        'invoice_date' => now()->toDateString(),
        'discount' => 0,
        'tax_type' => 'none',
        'items' => [
            ['product_name' => 'Widget', 'quantity' => 2, 'price' => 100],
        ],
    ])->assertRedirect(route('accounting.invoices.index'));

    $from->refresh();
    $to->refresh();

    expect((float) $from->total_purchases)->toBe(0.0)
        ->and((float) $from->balance)->toBe(0.0)
        ->and((float) $to->total_purchases)->toBe(200.0)
        ->and((float) $to->balance)->toBe(200.0);
});

it('carries only the outstanding amount when a partly paid invoice is reassigned', function () {
    $user = balanceAdmin();
    $from = balanceClient('From');
    $to = balanceClient('To');

    $invoice = balancePostInvoice($this, $user, $from);

    $this->actingAs($user)->post('/accounting/payments', [
        'invoice_id' => $invoice->id,
        'client_id' => $from->id,
        'amount' => 50,
        'payment_date' => now()->toDateString(),
    ])->assertRedirect(route('accounting.payments.index'));

    expect((float) $from->fresh()->balance)->toBe(150.0);

    $this->actingAs($user)->put("/accounting/invoices/{$invoice->id}", [
        'client_id' => $to->id,
        'client_name' => $to->name,
        'invoice_date' => now()->toDateString(),
        'discount' => 0,
        'tax_type' => 'none',
        'items' => [
            ['product_name' => 'Widget', 'quantity' => 2, 'price' => 100],
        ],
    ])->assertRedirect(route('accounting.invoices.index'));

    $from->refresh();
    $to->refresh();

    // The already-paid 50 is not outstanding and must not follow the invoice.
    expect((float) $from->total_purchases)->toBe(0.0)
        ->and((float) $from->balance)->toBe(0.0)
        ->and((float) $to->total_purchases)->toBe(200.0)
        ->and((float) $to->balance)->toBe(150.0);
});
