<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('invoice');
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('payment_number', 'like', "%{$s}%")
                    ->orWhere('reference', 'like', "%{$s}%");
            });
        }
        $payments = $query->latest()->paginate(10);

        return view('accounting.payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->orderBy('invoice_number')
            ->get(['id', 'invoice_number', 'client_name', 'due_amount']);
        $clients = Client::active()->orderBy('name')->get();

        return view('accounting.payments.create', compact('invoices', 'clients'));
    }

    public function show(Payment $payment)
    {
        $payment->load('invoice', 'client');

        return view('accounting.payments.show', compact('payment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_id' => 'nullable|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $payment = Payment::create([
                'payment_number' => Payment::generatePaymentNumber(),
                'invoice_id' => $validated['invoice_id'] ?? null,
                'client_id' => $validated['client_id'] ?? null,
                'type' => 'incoming',
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->applyPaymentToInvoice($validated['invoice_id'] ?? null, $validated['amount']);
            $this->adjustClientBalanceFromPayment($validated['invoice_id'] ?? null, $validated['amount'], 'decrement');

            Transaction::create([
                'transaction_number' => Transaction::generateTransactionNumber(),
                'user_id' => Auth::id(),
                'type' => 'payment',
                'category' => 'payment_received',
                'description' => "Payment {$payment->payment_number} received",
                'debit' => $validated['amount'],
                'credit' => 0,
                'balance' => $validated['amount'],
                'transaction_date' => $validated['payment_date'],
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
            ]);
        });

        return redirect()->route('accounting.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function edit(Payment $payment)
    {
        $invoices = Invoice::query()
            ->where(function ($q) use ($payment) {
                $q->whereIn('status', ['unpaid', 'partial'])
                    ->orWhere('id', $payment->invoice_id);
            })
            ->orderBy('invoice_number')
            ->get(['id', 'invoice_number', 'client_name']);
        $clients = Client::active()->orderBy('name')->get();

        return view('accounting.payments.edit', compact('payment', 'invoices', 'clients'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_id' => 'nullable|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $oldInvoiceId = $payment->invoice_id;
        $oldAmount = $payment->amount;

        DB::transaction(function () use ($validated, $payment, $oldInvoiceId, $oldAmount) {
            $payment->update($validated);

            // Reverse old payment effect
            $this->applyPaymentToInvoice($oldInvoiceId, -$oldAmount);
            $this->adjustClientBalanceFromPayment($oldInvoiceId, $oldAmount, 'increment');

            // Apply new payment effect
            $this->applyPaymentToInvoice($validated['invoice_id'] ?? null, $validated['amount']);
            $this->adjustClientBalanceFromPayment($validated['invoice_id'] ?? null, $validated['amount'], 'decrement');

            // Sync the linked ledger row model-by-model so the audit hook sees it.
            Transaction::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)
                ->get()
                ->each(fn (Transaction $transaction) => $transaction->update([
                    'debit' => $validated['amount'],
                    'credit' => 0,
                    'balance' => $validated['amount'],
                    'transaction_date' => $validated['payment_date'],
                ]));
        });

        return redirect()->route('accounting.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $invoiceId = $payment->invoice_id;
            $amount = $payment->amount;

            // Reverse payment effect
            $this->applyPaymentToInvoice($invoiceId, -$amount);
            $this->adjustClientBalanceFromPayment($invoiceId, $amount, 'increment');

            // Soft-delete the linked ledger row so the deletion itself is auditable
            // and no posted entry is ever physically removed.
            Transaction::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)
                ->get()
                ->each(fn (Transaction $transaction) => $transaction->delete());

            $payment->delete();
        });

        return redirect()->route('accounting.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    private function applyPaymentToInvoice(?int $invoiceId, float $amount): void
    {
        if (! $invoiceId || $amount == 0) {
            return;
        }

        $invoice = Invoice::find($invoiceId);
        if (! $invoice) {
            return;
        }

        $newPaid = max(0, $invoice->paid_amount + $amount);
        $newDue = max(0, $invoice->total - $newPaid);

        if ($newPaid <= 0) {
            $status = 'unpaid';
        } elseif ($newPaid >= $invoice->total) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $invoice->update([
            'paid_amount' => $newPaid,
            'due_amount' => $newDue,
            'status' => $status,
        ]);
    }

    private function adjustClientBalanceFromPayment(?int $invoiceId, float $amount, string $operation): void
    {
        if (! $invoiceId || $amount == 0) {
            return;
        }

        $invoice = Invoice::find($invoiceId);
        if (! $invoice || ! $invoice->client_id) {
            return;
        }

        $client = Client::find($invoice->client_id);
        if (! $client) {
            return;
        }

        if ($operation === 'increment') {
            $client->increment('balance', $amount);
        } else {
            $client->decrement('balance', $amount);
        }
    }
}
