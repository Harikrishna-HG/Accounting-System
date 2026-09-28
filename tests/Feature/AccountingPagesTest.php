<?php

use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedAccountingData(): array
{
    (new \Database\Seeders\RoleAndPermissionSeeder)->run();

    $user = User::factory()->create([
        'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
    ]);

    $product = Product::create([
        'name' => 'Widget', 'slug' => 'widget', 'sku' => 'W-1',
        'price' => 200, 'cost_price' => 100, 'stock_quantity' => 5, 'is_active' => true,
    ]);

    $client = Client::create(['name' => 'Acme Ltd', 'is_active' => true, 'balance' => 0]);
    $supplier = Supplier::create(['name' => 'Supply Co', 'is_active' => true, 'balance' => 0]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-0001', 'client_id' => $client->id, 'client_name' => 'Acme Ltd',
        'invoice_date' => now()->toDateString(), 'subtotal' => 200, 'total' => 200,
        'paid_amount' => 0, 'due_amount' => 200, 'status' => 'unpaid',
    ]);
    InvoiceItem::create([
        'invoice_id' => $invoice->id, 'product_id' => $product->id, 'product_name' => 'Widget',
        'quantity' => 1, 'price' => 200, 'total' => 200,
    ]);

    $paidInvoice = Invoice::create([
        'invoice_number' => 'INV-0002', 'client_id' => $client->id, 'client_name' => 'Acme Ltd',
        'invoice_date' => now()->toDateString(), 'subtotal' => 100, 'total' => 100,
        'paid_amount' => 100, 'due_amount' => 0, 'status' => 'paid',
    ]);

    $payment = Payment::create([
        'payment_number' => 'PAY-0001', 'invoice_id' => $invoice->id, 'client_id' => $client->id,
        'type' => 'incoming', 'amount' => 50, 'payment_date' => now()->toDateString(),
    ]);

    Expense::create([
        'expense_number' => 'EXP-0001', 'category' => 'rent', 'description' => 'Office rent',
        'amount' => 500, 'expense_date' => now()->toDateString(), 'supplier_id' => $supplier->id,
    ]);

    $order = PurchaseOrder::create([
        'order_number' => 'PO-0001', 'supplier_id' => $supplier->id, 'supplier_name' => 'Supply Co',
        'order_date' => now()->toDateString(), 'subtotal' => 200, 'total' => 200,
        'paid_amount' => 0, 'status' => 'pending',
    ]);
    PurchaseOrderItem::create([
        'purchase_order_id' => $order->id, 'product_id' => $product->id, 'product_name' => 'Widget',
        'quantity' => 1, 'price' => 200, 'total' => 200,
    ]);

    return compact('user', 'product', 'client', 'supplier', 'invoice', 'paidInvoice', 'payment', 'order');
}

it('renders every accounting index page', function (string $path) {
    ['user' => $user] = seedAccountingData();

    $this->actingAs($user)->get($path)->assertOk();
})->with([
    '/accounting/products',
    '/accounting/clients',
    '/accounting/suppliers',
    '/accounting/invoices',
    '/accounting/payments',
    '/accounting/expenses',
    '/accounting/purchase-orders',
    '/accounting/transactions',
    '/accounting/reports',
    // /accounting/reports/profit-loss is excluded: ReportController uses MySQL-only
    // DATE_FORMAT(), so it cannot run against the sqlite test connection.
]);

it('renders every accounting detail page', function (string $path, string $key) {
    $data = seedAccountingData();

    $this->actingAs($data['user'])->get(str_replace(':id', (string) $data[$key]->id, $path))->assertOk();
})->with([
    ['/accounting/clients/:id', 'client'],
    ['/accounting/suppliers/:id', 'supplier'],
    ['/accounting/invoices/:id', 'invoice'],
    ['/accounting/invoices/:id/print', 'invoice'],
    ['/accounting/payments/:id', 'payment'],
    ['/accounting/purchase-orders/:id', 'order'],
]);

it('renders every create and edit form', function (string $path) {
    ['user' => $user] = seedAccountingData();

    $this->actingAs($user)->get($path)->assertOk();
})->with([
    '/accounting/products/create',
    '/accounting/clients/create',
    '/accounting/suppliers/create',
    '/accounting/invoices/create',
    '/accounting/payments/create',
    '/accounting/expenses/create',
    '/accounting/purchase-orders/create',
]);

it('renders edit forms for existing records', function (string $path, string $key) {
    $data = seedAccountingData();

    $this->actingAs($data['user'])->get(str_replace(':id', (string) $data[$key]->id, $path))->assertOk();
})->with([
    ['/accounting/products/:id/edit', 'product'],
    ['/accounting/clients/:id/edit', 'client'],
    ['/accounting/suppliers/:id/edit', 'supplier'],
    ['/accounting/invoices/:id/edit', 'invoice'],
    ['/accounting/payments/:id/edit', 'payment'],
    ['/accounting/purchase-orders/:id/edit', 'order'],
]);

it('offers only payable invoices when creating a payment', function () {
    ['user' => $user, 'invoice' => $unpaid, 'paidInvoice' => $paid] = seedAccountingData();

    $response = $this->actingAs($user)->get('/accounting/payments/create')->assertOk();

    $response->assertSee($unpaid->invoice_number)
        ->assertDontSee($paid->invoice_number);
});

it('keeps the payments current invoice selectable when editing a payment', function () {
    ['user' => $user, 'paidInvoice' => $paid] = seedAccountingData();

    $payment = Payment::create([
        'payment_number' => 'PAY-0002', 'invoice_id' => $paid->id, 'client_id' => $paid->client_id,
        'type' => 'incoming', 'amount' => 100, 'payment_date' => now()->toDateString(),
    ]);

    $this->actingAs($user)
        ->get("/accounting/payments/{$payment->id}/edit")
        ->assertOk()
        ->assertSee($paid->invoice_number);
});
