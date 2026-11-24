<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $primaryKey = 'id_order_detail';

    protected $fillable = [
        'id_order',
        'id_product',
        'quantity',
        'variants_selected',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'variants_selected' => 'array',
            'quantity' => 'integer',
            'price' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }
}
