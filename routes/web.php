<?php

use App\Http\Controllers\Accounting\AccountingDashboardController;
use App\Http\Controllers\Accounting\ClientController as AccountingClientController;
use App\Http\Controllers\Accounting\ExpenseController as AccountingExpenseController;
use App\Http\Controllers\Accounting\InvoiceController as AccountingInvoiceController;
use App\Http\Controllers\Accounting\PaymentController as AccountingPaymentController;
use App\Http\Controllers\Accounting\ProductController as AccountingProductController;
use App\Http\Controllers\Accounting\PurchaseOrderController as AccountingPurchaseOrderController;
use App\Http\Controllers\Accounting\ReportController as AccountingReportController;
use App\Http\Controllers\Accounting\SupplierController as AccountingSupplierController;
use App\Http\Controllers\Accounting\TransactionController as AccountingTransactionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::middleware(['auth', 'permission:audit.view'])->group(function () {
    Route::get('/accounting/audit-logs', [AuditLogController::class, 'index'])
        ->name('accounting.audit-logs.index');
    Route::get('/accounting/audit-logs/{auditLog}', [AuditLogController::class, 'show'])
        ->name('accounting.audit-logs.show');
});

Route::match(['get', 'post'], '/login', [UserAuthController::class, 'login'])->name('login');
Route::get('/forgot-password', [UserAuthController::class, 'forgot_password'])->name('forgot-password');
Route::get('logout', [UserAuthController::class, 'logout'])->name('logout');

// Accounts are created by an administrator through the users panel, which assigns
// a role. Public self-registration is intentionally not routed: register() created
// users with no role_id, which left every self-registered account unable to open
// a single page after signing in.
Route::redirect('/register', '/login')->name('register');
Route::redirect('/signup', '/login');

Route::middleware('auth')->group(function () {
    // A signed-in account whose role holds no permissions would otherwise land on
    // a 403 with no way forward.
    Route::get('/no-access', [UserAuthController::class, 'noAccess'])->name('no-access');
});

Route::middleware(['auth', 'permission:dashboard.view'])->group(function () {
    // ===== Accounting Management Routes =====
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/dashboard', [AccountingDashboardController::class, 'index'])->name('dashboard');

        Route::middleware('permission:products.manage')->group(function () {
            Route::get('/products', [AccountingProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [AccountingProductController::class, 'create'])->name('products.create');
            Route::post('/products', [AccountingProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}', [AccountingProductController::class, 'show'])->name('products.show');
            Route::get('/products/{product}/edit', [AccountingProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [AccountingProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [AccountingProductController::class, 'destroy'])->name('products.destroy');
        });

        Route::middleware('permission:clients.manage')->group(function () {
            Route::get('/clients', [AccountingClientController::class, 'index'])->name('clients.index');
            Route::get('/clients/create', [AccountingClientController::class, 'create'])->name('clients.create');
            Route::post('/clients', [AccountingClientController::class, 'store'])->name('clients.store');
            Route::get('/clients/{client}', [AccountingClientController::class, 'show'])->name('clients.show');
            Route::get('/clients/{client}/edit', [AccountingClientController::class, 'edit'])->name('clients.edit');
            Route::put('/clients/{client}', [AccountingClientController::class, 'update'])->name('clients.update');
            Route::delete('/clients/{client}', [AccountingClientController::class, 'destroy'])->name('clients.destroy');
        });

        Route::middleware('permission:suppliers.manage')->group(function () {
            Route::get('/suppliers', [AccountingSupplierController::class, 'index'])->name('suppliers.index');
            Route::get('/suppliers/create', [AccountingSupplierController::class, 'create'])->name('suppliers.create');
            Route::post('/suppliers', [AccountingSupplierController::class, 'store'])->name('suppliers.store');
            Route::get('/suppliers/{supplier}', [AccountingSupplierController::class, 'show'])->name('suppliers.show');
            Route::get('/suppliers/{supplier}/edit', [AccountingSupplierController::class, 'edit'])->name('suppliers.edit');
            Route::put('/suppliers/{supplier}', [AccountingSupplierController::class, 'update'])->name('suppliers.update');
            Route::delete('/suppliers/{supplier}', [AccountingSupplierController::class, 'destroy'])->name('suppliers.destroy');
        });

        Route::middleware('permission:invoices.manage')->group(function () {
            Route::get('/invoices', [AccountingInvoiceController::class, 'index'])->name('invoices.index');
            Route::get('/invoices/create', [AccountingInvoiceController::class, 'create'])->name('invoices.create');
            Route::post('/invoices', [AccountingInvoiceController::class, 'store'])->name('invoices.store');
            Route::get('/invoices/{invoice}', [AccountingInvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{invoice}/edit', [AccountingInvoiceController::class, 'edit'])->name('invoices.edit');
            Route::put('/invoices/{invoice}', [AccountingInvoiceController::class, 'update'])->name('invoices.update');
            Route::delete('/invoices/{invoice}', [AccountingInvoiceController::class, 'destroy'])->name('invoices.destroy');
            Route::get('/invoices/{invoice}/print', [AccountingInvoiceController::class, 'print'])->name('invoices.print');
        });

        Route::middleware('permission:payments.manage')->group(function () {
            Route::get('/payments', [AccountingPaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/create', [AccountingPaymentController::class, 'create'])->name('payments.create');
            Route::post('/payments', [AccountingPaymentController::class, 'store'])->name('payments.store');
            Route::get('/payments/{payment}', [AccountingPaymentController::class, 'show'])->name('payments.show');
            Route::get('/payments/{payment}/edit', [AccountingPaymentController::class, 'edit'])->name('payments.edit');
            Route::put('/payments/{payment}', [AccountingPaymentController::class, 'update'])->name('payments.update');
            Route::delete('/payments/{payment}', [AccountingPaymentController::class, 'destroy'])->name('payments.destroy');
        });

        Route::middleware('permission:expenses.manage')->group(function () {
            Route::get('/expenses', [AccountingExpenseController::class, 'index'])->name('expenses.index');
            Route::get('/expenses/create', [AccountingExpenseController::class, 'create'])->name('expenses.create');
            Route::post('/expenses', [AccountingExpenseController::class, 'store'])->name('expenses.store');
            Route::get('/expenses/{expense}', [AccountingExpenseController::class, 'show'])->name('expenses.show');
            Route::get('/expenses/{expense}/edit', [AccountingExpenseController::class, 'edit'])->name('expenses.edit');
            Route::put('/expenses/{expense}', [AccountingExpenseController::class, 'update'])->name('expenses.update');
            Route::delete('/expenses/{expense}', [AccountingExpenseController::class, 'destroy'])->name('expenses.destroy');
        });

        Route::middleware('permission:reports.view')->group(function () {
            // The ledger is a read-only financial view, so it shares reports.view.
            Route::get('/transactions', [AccountingTransactionController::class, 'index'])->name('transactions.index');

            Route::get('/reports', [AccountingReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/profit-loss', [AccountingReportController::class, 'profitLoss'])->name('reports.profit-loss');
        });

        Route::middleware('permission:purchase-orders.manage')->group(function () {
            Route::get('/purchase-orders', [AccountingPurchaseOrderController::class, 'index'])->name('purchase-orders.index');
            Route::get('/purchase-orders/create', [AccountingPurchaseOrderController::class, 'create'])->name('purchase-orders.create');
            Route::post('/purchase-orders', [AccountingPurchaseOrderController::class, 'store'])->name('purchase-orders.store');
            Route::get('/purchase-orders/{purchaseOrder}', [AccountingPurchaseOrderController::class, 'show'])->name('purchase-orders.show');
            Route::get('/purchase-orders/{purchaseOrder}/edit', [AccountingPurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
            Route::put('/purchase-orders/{purchaseOrder}', [AccountingPurchaseOrderController::class, 'update'])->name('purchase-orders.update');
            Route::delete('/purchase-orders/{purchaseOrder}', [AccountingPurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
        });
    });

    Route::redirect('/dashboard', '/accounting/dashboard')->name('dashboard');
});

// The root path is the landing page after sign-in, so it follows the same gate.
Route::redirect('/', '/accounting/dashboard')->middleware(['auth', 'permission:dashboard.view']);

Route::middleware(['auth', 'permission:users.manage'])->group(function () {
    Route::resource('/dashboard/users', UserController::class)
        ->names('dashboard.users')
        ->except(['show']);
});

Route::middleware(['auth', 'permission:roles.manage'])->group(function () {
    Route::resource('/dashboard/roles', RoleController::class)
        ->names('dashboard.roles')
        ->except(['show']);
});
