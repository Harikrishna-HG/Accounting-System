<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $base = Transaction::query();
        if ($request->filled('type')) {
            $base->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $base->where('category', $request->category);
        }
        if ($request->filled('from')) {
            $base->where('transaction_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $base->where('transaction_date', '<=', $request->to);
        }

        $totalsQuery = $base->clone();
        $totalDebit = $totalsQuery->sum('debit');
        $totalCredit = $totalsQuery->sum('credit');

        $transactions = $base->latest()->paginate(20);

        return view('accounting.transactions.index', compact('transactions', 'totalDebit', 'totalCredit'));
    }
}
