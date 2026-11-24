<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    public const TYPE_PERCENT = 1;

    public const TYPE_FIXED = 2;

    public const TYPE_SHIPPING = 3;

    protected $primaryKey = 'id_promotion';

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'value' => 'integer',
            'min_order_value' => 'integer',
            'quantity' => 'integer',
            'used' => 'integer',
            'status' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function isSoldOut(): bool
    {
        return $this->used >= $this->quantity;
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->status === 1
            && $this->start_date->lte(now())
            && $this->end_date->gte(now())
            && ! $this->isSoldOut();
    }

    public function discountAmount(int $subtotal, int $shippingFee): int
    {
        if ($subtotal < $this->min_order_value) {
            return 0;
        }

        return match ($this->type) {
            self::TYPE_PERCENT => min($subtotal, intdiv($subtotal * $this->value, 100)),
            self::TYPE_FIXED => min($subtotal, $this->value),
            self::TYPE_SHIPPING => min($shippingFee, $this->value),
            default => 0,
        };
    }
}
