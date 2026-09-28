<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhere('client_name', 'like', "%{$s}%");
            });
        }
        $invoices = $query->latest()->paginate(10);

        return view('accounting.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        return view('accounting.invoices.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required_without:client_id|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_address' => 'nullable|string',
            'client_pan' => 'nullable|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|in:none,vat,service',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $invoiceNumber = Invoice::generateInvoiceNumber();
        $subtotal = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            $subtotal += $total;
            $itemsData[] = [
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $total,
            ];
        }

        $discount = $validated['discount'] ?? 0;
        $taxable = $subtotal - $discount;
        $tax = 0;
        if (($validated['tax_type'] ?? 'none') === 'vat') {
            $tax = $taxable * 0.13;
        } elseif (($validated['tax_type'] ?? 'none') === 'service') {
            $tax = $taxable * 0.05;
        }
        $total = $taxable + $tax;

        DB::transaction(function () use ($validated, $invoiceNumber, $subtotal, $itemsData, $discount, $tax, $total) {
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'client_id' => $validated['client_id'] ?? null,
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_pan' => $validated['client_pan'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'tax_type' => $validated['tax_type'] ?? 'none',
                'total' => $total,
                'paid_amount' => 0,
                'due_amount' => $total,
                'status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
            ]);

            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            $clientId = $validated['client_id'] ?? null;

            if ($clientId) {
                $client = Client::find($clientId);
                if ($client) {
                    $client->increment('total_purchases', $total);
                    $client->increment('balance', $total);
                }
            }

            Transaction::create([
                'transaction_number' => Transaction::generateTransactionNumber(),
                'user_id' => Auth::id(),
                'type' => 'invoice',
                'category' => 'sales',
                'description' => "Invoice {$invoiceNumber} - {$validated['client_name']}",
                'debit' => 0,
                'credit' => $total,
                'balance' => $total,
                'transaction_date' => $validated['invoice_date'],
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
            ]);
        });

        return redirect()->route('accounting.invoices.index')
            ->with('success', "Invoice {$invoiceNumber} created successfully.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'payments');

        return view('accounting.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $clients = Client::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $invoice->load('items');

        return view('accounting.invoices.edit', compact('invoice', 'clients', 'products'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_address' => 'nullable|string',
            'client_pan' => 'nullable|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|in:none,vat,service',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:unpaid,partial,paid,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $oldTotal = $invoice->total;
        $oldClientId = $invoice->client_id;
        $oldInvoiceDate = $invoice->invoice_date;

        $subtotal = 0;
        $itemsData = [];
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            $subtotal += $total;
            $itemsData[] = $item + ['total' => $total];
        }

        $discount = $validated['discount'] ?? 0;
        $taxable = $subtotal - $discount;
        $tax = 0;
        if (($validated['tax_type'] ?? 'none') === 'vat') {
            $tax = $taxable * 0.13;
        } elseif (($validated['tax_type'] ?? 'none') === 'service') {
            $tax = $taxable * 0.05;
        }
        $total = $taxable + $tax;
        $paidAmount = $invoice->paid_amount;
        $dueAmount = max(0, $total - $paidAmount);

        $newStatus = $validated['status'] ?? null;
        if (! $newStatus || $newStatus === 'unpaid') {
            $newStatus = $paidAmount >= $total ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');
            if ($paidAmount <= 0) {
                $newStatus = 'unpaid';
            }
        }

        DB::transaction(function () use ($validated, $invoice, $subtotal, $itemsData, $discount, $tax, $total, $dueAmount, $newStatus, $oldTotal, $oldClientId) {
            $invoice->update([
                'client_id' => $validated['client_id'] ?? null,
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_pan' => $validated['client_pan'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'tax_type' => $validated['tax_type'] ?? 'none',
                'total' => $total,
                'due_amount' => $dueAmount,
                'status' => $newStatus,
                'notes' => $validated['notes'] ?? null,
            ]);

            $invoice->items()->delete();
            foreach ($itemsData as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            // Adjust client balances on client_id reassignment
            if ((int) $oldClientId !== (int) ($validated['client_id'] ?? 0) && $oldClientId) {
                $oldClient = Client::find($oldClientId);
                if ($oldClient) {
                    $oldClient->decrement('total_purchases', $oldTotal);
                    $oldClient->decrement('balance', $oldTotal);
                }
            }
            $newClientId = $validated['client_id'] ?? null;

            if ($newClientId) {
                $newClient = Client::find($newClientId);
                if ($newClient) {
                    $diffTotal = $total - ((int) $oldClientId === (int) $newClientId ? $oldTotal : 0);
                    if ($diffTotal != 0) {
                        $newClient->increment('total_purchases', $diffTotal);
                        $newClient->increment('balance', $diffTotal);
                    }
                }
            }

            // Sync the linked ledger row model-by-model so the audit hook sees it.
            Transaction::where('reference_type', Invoice::class)
                ->where('reference_id', $invoice->id)
                ->get()
                ->each(fn (Transaction $transaction) => $transaction->update([
                    'credit' => $total,
                    'balance' => $total,
                    'description' => "Invoice {$invoice->invoice_number} - {$validated['client_name']}",
                    'transaction_date' => $validated['invoice_date'],
                ]));
        });

        return redirect()->route('accounting.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $clientId = $invoice->client_id;
            $total = $invoice->total;

            $invoice->items()->delete();

            // Reverse client balance
            if ($clientId) {
                $client = Client::find($clientId);
                if ($client) {
                    $client->decrement('total_purchases', $total);
                    $client->decrement('balance', $total);
                }
            }

            // Soft-delete the linked ledger row so the deletion itself is auditable
            // and no posted entry is ever physically removed.
            Transaction::where('reference_type', Invoice::class)
                ->where('reference_id', $invoice->id)
                ->get()
                ->each(fn (Transaction $transaction) => $transaction->delete());

            $invoice->delete();
        });

        return redirect()->route('accounting.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('items');

        return view('accounting.invoices.print', compact('invoice'));
    }
}
