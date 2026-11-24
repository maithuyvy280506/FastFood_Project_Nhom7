<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 0;

    public const STATUS_PREPARING = 1;

    public const STATUS_DELIVERING = 2;

    public const STATUS_DELIVERED = 3;

    public const STATUS_CANCELLED = 4;

    protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_user',
        'customer_name',
        'customer_phone',
        'customer_address',
        'id_promotion',
        'discount_amount',
        'total_price',
        'note',
        'shipping_fee',
        'status',
        'method',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'method' => 'integer',
            'discount_amount' => 'integer',
            'total_price' => 'integer',
            'shipping_fee' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'id_order', 'id_order');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_PREPARING => 'Đang chuẩn bị',
            self::STATUS_DELIVERING => 'Đang giao',
            self::STATUS_DELIVERED => 'Đã giao',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => '#e67e22',
            self::STATUS_PREPARING => '#3498db',
            self::STATUS_DELIVERING => '#9b59b6',
            self::STATUS_DELIVERED => '#27ae60',
            self::STATUS_CANCELLED => '#e74c3c',
            default => '#7f8c8d',
        };
    }

    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
