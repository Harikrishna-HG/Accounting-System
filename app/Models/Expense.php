<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_number', 'category', 'description', 'amount', 'expense_date',
        'payment_method', 'reference', 'supplier_id', 'receipt', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function generateExpenseNumber()
    {
        $prefix = 'EXP-' . date('Ymd');
        $last = self::where('expense_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $num = $last ? intval(substr($last->expense_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
