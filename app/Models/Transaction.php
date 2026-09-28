<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number', 'type', 'category', 'description', 'debit', 'credit',
        'balance', 'transaction_date', 'reference_type', 'reference_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public static function generateTransactionNumber()
    {
        $prefix = 'TXN-' . date('Ymd');
        $last = self::where('transaction_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $num = $last ? intval(substr($last->transaction_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
