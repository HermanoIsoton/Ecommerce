<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'subtotal_cents',
        'shipping_cents',
        'total_cents',
        'ship_recipient',
        'ship_cep',
        'ship_street',
        'ship_number',
        'ship_complement',
        'ship_district',
        'ship_city',
        'ship_state',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'payment_expires_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Order_item(): HasMany
    {
        return $this->hasMany(Order_item::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(Order_item::class, 'order_id');
    }

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'payment_expires_at' => 'datetime',
        ];
    }
}
