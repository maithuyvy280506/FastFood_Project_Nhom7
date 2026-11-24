<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $primaryKey = 'id_product';

    public $timestamps = false;

    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'isFlashSale' => 'boolean',
            'is_active' => 'boolean',
            'variants' => 'array',
        ];
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'id_product', 'id_product');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'id_product', 'id_product');
    }
}
