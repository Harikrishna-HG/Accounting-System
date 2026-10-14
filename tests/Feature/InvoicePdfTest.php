<?php

use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use App\Support\NepaliDate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function pdfAdmin(): User
{
    (new \Database\Seeders\RoleAndPermissionSeeder)->run();

    return User::factory()->create([
        'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
    ]);
}

function pdfInvoice(): Invoice
{
    $invoice = Invoice::create([
        'invoice_number' => 'INV-'.date('Ymd').'-0001',
        'client_name' => 'Ram Bahadur',
        'client_address' => 'Kathmandu',
        'invoice_date' => '2025-04-14',
        'subtotal' => 200,
        'discount' => 0,
        'tax' => 26,
        'tax_type' => 'vat',
        'total' => 226,
        'paid_amount' => 0,
        'due_amount' => 226,
        'status' => 'unpaid',
    ]);

    $invoice->items()->create([
        'product_name' => 'Widget',
        'quantity' => 2,
        'price' => 100,
        'total' => 200,
    ]);

    return $invoice;
}

it('formats an AD date as a Devanagari Bikram Sambat date', function () {
    expect(NepaliDate::format(new DateTimeImmutable('2025-04-14')))->toBe('२०८२ वैशाख १')
        ->and(NepaliDate::format(new DateTimeImmutable('2024-04-13')))->toBe('२०८१ वैशाख १')
        ->and(NepaliDate::format(null))->toBe('');
});

it('falls back to the Gregorian date when the AD year is outside the BS range', function () {
    expect(NepaliDate::format(new DateTimeImmutable('1900-01-01')))->toBe('1900-01-01');
});

it('renders the invoice PDF view with English and Nepali dates and the business name', function () {
    config()->set('accounting.business.name', 'Himalaya Traders');

    $html = view('accounting.invoices.pdf', [
        'invoice' => pdfInvoice(),
        'business' => config('accounting.business'),
    ])->render();

    expect($html)->toContain('Himalaya Traders')
        ->toContain('2025-04-14')
        ->toContain('२०८२ वैशाख १')
        ->toContain('Ram Bahadur')
        ->toContain('Widget')
        // The amount summary is a bordered, tabular grid with a header row.
        ->toContain('class="amounts"')
        ->toContain('Amount (NPR)')
        ->toContain('226.00');
});

it('shows the business name in the invoice header', function () {
    $html = view('accounting.invoices.pdf', [
        'invoice' => pdfInvoice(),
        'business' => config('accounting.business'),
    ])->render();

    expect($html)->toContain('class="company-header"')
        ->and($html)->toContain('class="company-name"')
        ->and($html)->toContain('Rate (NPR)')
        ->and($html)->toContain('Balance Due');
});

it('downloads the invoice as a PDF attachment', function () {
    $user = pdfAdmin();
    $invoice = pdfInvoice();

    $response = $this->actingAs($user)
        ->get(route('accounting.invoices.print', $invoice));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    expect($response->headers->get('content-disposition'))
        ->toContain('attachment')
        ->toContain($invoice->invoice_number);

    expect($response->getContent())->toStartWith('%PDF');
});
