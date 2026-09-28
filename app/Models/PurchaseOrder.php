<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'order_number', 'supplier_id', 'supplier_name', 'order_date', 'expected_date',
        'subtotal', 'tax', 'total', 'paid_amount', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public static function generateOrderNumber()
    {
        $prefix = 'PO-'.date('Ymd');
        $last = self::where('order_number', 'like', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();
        $num = $last ? intval(substr($last->order_number, -4)) + 1 : 1;

        return $prefix.'-'.str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
