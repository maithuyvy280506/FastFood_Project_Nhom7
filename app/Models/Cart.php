<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $primaryKey = 'id_cart';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_product',
        'quantity',
        'note',
        'variants_selected',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'variants_selected' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }
}
