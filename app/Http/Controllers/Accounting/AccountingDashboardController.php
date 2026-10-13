<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AccountingDashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $cacheKey = 'dashboard.totals.'.sha1($startOfMonth.'|'.$endOfMonth);

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            $totalRevenue = $cached['totalRevenue'] ?? 0;
            $totalDue = $cached['totalDue'] ?? 0;
            $totalExpenses = $cached['totalExpenses'] ?? 0;
            $totalProducts = $cached['totalProducts'] ?? 0;
            $totalClients = $cached['totalClients'] ?? 0;
            $totalSuppliers = $cached['totalSuppliers'] ?? 0;
            $monthlyRevenue = $cached['monthlyRevenue'] ?? 0;
            $monthlyExpenses = $cached['monthlyExpenses'] ?? 0;
        } else {
            $totalRevenue = Invoice::where('status', 'paid')->sum('total');
            $totalDue = Invoice::whereIn('status', ['unpaid', 'partial'])->sum('due_amount');
            $totalExpenses = Expense::sum('amount');
            $totalProducts = Product::count();
            $totalClients = Client::count();
            $totalSuppliers = Supplier::count();

            $monthlyRevenue = Invoice::where('status', 'paid')
                ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                ->sum('total');

            $monthlyExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            Cache::put($cacheKey, [
                'totalRevenue' => $totalRevenue,
                'totalDue' => $totalDue,
                'totalExpenses' => $totalExpenses,
                'totalProducts' => $totalProducts,
                'totalClients' => $totalClients,
                'totalSuppliers' => $totalSuppliers,
                'monthlyRevenue' => $monthlyRevenue,
                'monthlyExpenses' => $monthlyExpenses,
            ], 60);
        }

        $recentInvoices = Invoice::with('client')->latest()->take(5)->get();
        $recentPayments = Payment::latest()->take(5)->get();
        $recentExpenses = Expense::latest()->take(5)->get();

        return view('accounting.dashboard', compact(
            'totalRevenue',
            'totalDue',
            'totalExpenses',
            'totalProducts',
            'totalClients',
            'totalSuppliers',
            'recentInvoices',
            'recentPayments',
            'recentExpenses',
            'monthlyRevenue',
            'monthlyExpenses'
        ));
    }
}
