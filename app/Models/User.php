<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function address(): HasMany
    {
        return $this->hasMany(address::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(cart::class);
    }
}
