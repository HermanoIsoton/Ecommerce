<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'cancelled_at'
    ];

    public function user(): belongsTo{
        return $this->belongsTo(User::class);
    }
     public function Order_item(): hasMany{
        return $this->hasMany(Order_item::class);
    }
}
