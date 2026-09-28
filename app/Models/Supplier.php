<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'company', 'pan_vat',
        'total_purchases', 'balance', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_purchases' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
