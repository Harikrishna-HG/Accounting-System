<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('expense_number', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }
        $expenses = $query->latest()->paginate(10);
        $categories = Expense::select('category')->distinct()->pluck('category');
        return view('accounting.expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        return view('accounting.expenses.create', compact('suppliers'));
    }

    public function show(Expense $expense)
    {
        $expense->load('supplier');
        return view('accounting.expenses.show', compact('expense'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        $expense = Expense::create([
            'expense_number' => Expense::generateExpenseNumber(),
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'payment_method' => $validated['payment_method'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'receipt' => $receiptPath,
        ]);

        Transaction::create([
            'transaction_number' => Transaction::generateTransactionNumber(),
            'type' => 'expense',
            'category' => $validated['category'],
            'description' => $validated['description'] ?? "Expense {$expense->expense_number}",
            'debit' => $validated['amount'],
            'credit' => 0,
            'balance' => -$validated['amount'],
            'transaction_date' => $validated['expense_date'],
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
        ]);

        return redirect()->route('accounting.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        return view('accounting.expenses.edit', compact('expense', 'suppliers'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        $expense->update($validated);

        Transaction::where('reference_type', Expense::class)
            ->where('reference_id', $expense->id)
            ->update([
                'category' => $validated['category'],
                'description' => $validated['description'] ?? "Expense {$expense->expense_number}",
                'debit' => $validated['amount'],
                'credit' => 0,
                'balance' => -$validated['amount'],
                'transaction_date' => $validated['expense_date'],
            ]);

        return redirect()->route('accounting.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        Transaction::where('reference_type', Expense::class)
            ->where('reference_id', $expense->id)
            ->delete();

        $expense->delete();

        return redirect()->route('accounting.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
