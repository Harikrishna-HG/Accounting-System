<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('category');
            $table->index('is_active');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
            $table->index('invoice_date');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('type');
            $table->index('payment_date');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('category');
            $table->index('expense_date');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('type');
            $table->index('category');
            $table->index('transaction_date');
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['invoice_date']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['payment_date']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['expense_date']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['category']);
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['reference_type', 'reference_id']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['order_date']);
        });
    }
};
