<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Cart_item extends Model
{
     protected $fillable = [
        'id',
        'name_id', 
        'product_id',
        'quantity'
    ];
    public $timestamps = false;

      public function Cart(): belongsTo{
        return $this->belongsTo(Cart::class);
    }
     public function Product(): belongsTo{
        return $this->belongsTo(Product::class);
    }
}
