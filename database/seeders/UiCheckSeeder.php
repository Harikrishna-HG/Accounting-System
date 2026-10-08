<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UiCheckSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $role = \App\Models\Role::where('slug', 'admin')->first()
            ?? \App\Models\Role::orderBy('id')->first();

        $user = User::create([
            'name' => 'UI Check',
            'email' => 'uicheck@example.test',
            'password' => Hash::make('uicheck123'),
        ]);
        if ($role) {
            $user->role_id = $role->id;
            $user->save();
        }

        $client = Client::create([
            'name' => 'Acme Industries',
            'email' => 'ap@acme.test',
            'phone' => '9876543210',
            'address' => '12 Industrial Estate, Phase II',
            'company' => 'Acme Industries Pvt Ltd',
            'pan_vat' => 'AAACA1234A',
            'total_purchases' => 125000,
            'balance' => 45000,
        ]);

        $supplier = Supplier::create([
            'name' => 'Global Traders',
            'email' => 'sales@global.test',
            'phone' => '9123456780',
            'address' => '44 Market Road',
            'company' => 'Global Traders',
            'pan_vat' => 'BBBCB5678B',
            'total_purchases' => 88000,
            'balance' => -22000,
        ]);

        $product = Product::create([
            'name' => 'Steel Rod 12mm',
            'slug' => Str::slug('Steel Rod 12mm'),
            'category' => 'Raw Material',
            'description' => 'TMT steel rod, 12mm diameter, 12m length, ISI marked.',
            'price' => 4750,
            'cost_price' => 4100,
            'stock_quantity' => 320,
            'sku' => 'SKU-STEEL-12',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-UIC-0001',
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_phone' => $client->phone,
            'client_address' => $client->address,
            'client_pan' => $client->pan_vat,
            'invoice_date' => now()->subDays(20)->toDateString(),
            'due_date' => now()->addDays(10)->toDateString(),
            'subtotal' => 95000,
            'discount' => 2000,
            'tax' => 13950,
            'tax_type' => 'gst18',
            'total' => 106950,
            'paid_amount' => 61950,
            'due_amount' => 45000,
            'status' => 'partial',
            'notes' => 'Deliver to warehouse gate 2. Call before dispatch.',
            'payment_method' => 'bank_transfer',
        ]);

        Payment::create([
            'payment_number' => 'PAY-UIC-0001',
            'invoice_id' => $invoice->id,
            'client_id' => $client->id,
            'type' => 'incoming',
            'amount' => 61950,
            'payment_method' => 'bank_transfer',
            'reference' => 'UTR88123456',
            'payment_date' => now()->subDays(10)->toDateString(),
            'notes' => 'Partial settlement received.',
        ]);

        Expense::create([
            'expense_number' => 'EXP-UIC-0001',
            'category' => 'rent',
            'description' => 'Monthly warehouse rent and maintenance.',
            'amount' => 35000,
            'expense_date' => now()->subDays(5)->toDateString(),
            'payment_method' => 'bank_transfer',
            'reference' => 'RENT-SEP',
            'supplier_id' => $supplier->id,
            'notes' => 'Includes property tax share.',
        ]);

        $po = PurchaseOrder::create([
            'order_number' => 'PO-UIC-0001',
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'order_date' => now()->subDays(8)->toDateString(),
            'expected_date' => now()->addDays(6)->toDateString(),
            'subtotal' => 164000,
            'tax' => 29520,
            'total' => 193520,
            'paid_amount' => 100000,
            'status' => 'partial',
            'notes' => 'Second consignment against contract.',
        ]);

        $po->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 40,
            'price' => 4100,
            'total' => 164000,
        ]);
    }
}
