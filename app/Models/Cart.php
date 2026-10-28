<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Cart extends Model
{
     protected $fillable = [
        'id',
        'user_id',
        'token_hash'
    ];

      public function User(): belongsTo{
        return $this->belongsTo(User::class);
    }
     public function Cart_item(): hasMany{
        return $this->hasMany(Cart_item::class);
    }
}
