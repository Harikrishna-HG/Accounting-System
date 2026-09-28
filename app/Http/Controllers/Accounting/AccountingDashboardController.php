<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Client;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AccountingDashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $totalDue = Invoice::whereIn('status', ['unpaid', 'partial'])->sum('due_amount');
        $totalExpenses = Expense::sum('amount');
        $totalProducts = Product::count();
        $totalClients = Client::count();
        $totalSuppliers = Supplier::count();

        $recentInvoices = Invoice::with('client')->latest()->take(5)->get();
        $recentPayments = Payment::latest()->take(5)->get();
        $recentExpenses = Expense::latest()->take(5)->get();

        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('invoice_date', date('Y'))
            ->whereMonth('invoice_date', date('m'))
            ->sum('total');

        $monthlyExpenses = Expense::whereYear('expense_date', date('Y'))
            ->whereMonth('expense_date', date('m'))
            ->sum('amount');

        return view('accounting.dashboard', compact(
            'totalRevenue', 'totalDue', 'totalExpenses', 'totalProducts',
            'totalClients', 'totalSuppliers', 'recentInvoices', 'recentPayments',
            'recentExpenses', 'monthlyRevenue', 'monthlyExpenses'
        ));
    }
}
