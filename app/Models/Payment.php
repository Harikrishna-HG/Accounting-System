<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_number', 'invoice_id', 'client_id', 'supplier_id', 'type',
        'amount', 'payment_method', 'reference', 'payment_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function generatePaymentNumber()
    {
        $prefix = 'PAY-' . date('Ymd');
        $last = self::where('payment_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $num = $last ? intval(substr($last->payment_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
