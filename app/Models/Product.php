<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'category', 'description', 'price', 'cost_price',
        'stock_quantity', 'sku', 'image_url', 'source_url', 'source', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
