<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Financial records are never physically removed, so an issued invoice or a
     * settled payment can always be recovered and always remains auditable.
     */
    private const TABLES = [
        'clients',
        'expenses',
        'invoices',
        'payments',
        'products',
        'purchase_orders',
        'suppliers',
        'transactions',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropSoftDeletes();
            });
        }
    }
};
