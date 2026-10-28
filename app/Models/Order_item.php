<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Order_item extends Model
{
      protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'subtotal_cents'
    ];
    public $timestamps = false;

    public function order(): belongsTo{
        return $this->belongsTo(Order::class);
    }
     public function Product(): belongsTo{
        return $this->belongsTo(Product::class);
    }
}
