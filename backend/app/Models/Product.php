<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image_url',
        'price',
        'stock',
        'is_active',
        'model_year',
        'seating_capacity',
        'doors_count',
        'trunk_capacity_l',
        'type',
        'power',
        'range_km',
        'top_speed_kmh',
        'weight_kg',
        'charge_time_h',
        'battery_capaciy',
        'max_charge_power_kw',
        'battery_voltage_v',
        'is_battery_removable',
        'warranty_months',
    ];

    

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(Order_item::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart_item::class);
    }
}
