<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::latest();
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('from')) {
            $query->where('transaction_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('transaction_date', '<=', $request->to);
        }
        $transactions = $query->paginate(20);
        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        return view('accounting.transactions.index', compact('transactions', 'totalDebit', 'totalCredit'));
    }
}
