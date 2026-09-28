<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('accounting.reports.index');
    }

    public function profitLoss(Request $request)
    {
        $from = $request->from ?: now()->startOfMonth()->toDateString();
        $to = $request->to ?: now()->toDateString();

        $revenue = Invoice::where('status', 'paid')
            ->whereBetween('invoice_date', [$from, $to])
            ->sum('total');

        $expenses = Expense::whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $netProfit = $revenue - $expenses;

        $revenueByMonth = Invoice::where('status', 'paid')
            ->whereBetween('invoice_date', [$from, $to])
            ->selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $expensesByMonth = Expense::whereBetween('expense_date', [$from, $to])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $expensesByCategory = Expense::whereBetween('expense_date', [$from, $to])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category');

        return view('accounting.reports.profit-loss', compact(
            'from', 'to', 'revenue', 'expenses', 'netProfit',
            'revenueByMonth', 'expensesByMonth', 'expensesByCategory'
        ));
    }
}
