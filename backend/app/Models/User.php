<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

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
