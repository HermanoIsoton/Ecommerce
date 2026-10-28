<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class User extends Model
{
    protected $fillable = [
        'id',
        'name', 
        'email',
        'password_hash', 
        'role'
    ];

    public function address(): hasMany{
        return $this->hasMany(address::class);
    }
     public function order(): hasMany{
        return $this->hasMany(order::class);
    }
     public function cart(): hasOne{
        return $this->hasOne(cart::class);
    }
}
