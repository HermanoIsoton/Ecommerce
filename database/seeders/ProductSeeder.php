<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('products')->insert([
            [
                'category_id' => 1,
                'name' => 'Carro Elétrico Urbano E4',
                'slug' => 'carro-eletrico-urbano-e4',
                'description' => 'Carro elétrico compacto para deslocamentos urbanos, com quatro lugares e recarga rápida.',
                'image_url' => '/images/products/carro-eletrico-urbano-e4.jpg',
                'price' => 119900.00,
                'stock' => 5,
                'is_active' => true,
                'deleted_at' => $now,
                'model_year' => 2026,
                'seating_capacity' => 4,
                'doors_count' => 4,
                'trunk_capacity_l' => 300,
                'power' => 100000,
                'range_km' => 300,
                'top_speed_kmh' => 150,
                'weight_kg' => 1450.00,
                'charge_time_h' => 8.00,
                'battery_capaciy' => 40.00,
                'max_charge_power_kw' => 50,
                'battery_voltage_v' => 400.0,
                'is_battery_removable' => false,
                'warranty_months' => 36,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 2,
                'name' => 'Autopropelido City',
                'slug' => 'autopropelido-city',
                'description' => 'Veículo elétrico autopropelido leve para trajetos curtos e uso individual.',
                'image_url' => '/images/products/autopropelido-city.jpg',
                'price' => 7990.00,
                'stock' => 10,
                'is_active' => true,
                'deleted_at' => $now,
                'model_year' => 2026,
                'seating_capacity' => 1,
                'doors_count' => 0,
                'trunk_capacity_l' => null,
                'power' => 1000,
                'range_km' => 40,
                'top_speed_kmh' => 32,
                'weight_kg' => 45.00,
                'charge_time_h' => 6.00,
                'battery_capaciy' => 0.60,
                'max_charge_power_kw' => null,
                'battery_voltage_v' => 48.0,
                'is_battery_removable' => true,
                'warranty_months' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 3,
                'name' => 'Ciclomotor Elétrico E-Moto',
                'slug' => 'ciclomotor-eletrico-e-moto',
                'description' => 'Ciclomotor elétrico para deslocamentos urbanos, com dois lugares e bateria removível.',
                'image_url' => '/images/products/ciclomotor-eletrico-e-moto.jpg',
                'price' => 15990.00,
                'stock' => 7,
                'is_active' => true,
                'deleted_at' => $now,
                'model_year' => 2026,
                'seating_capacity' => 2,
                'doors_count' => 0,
                'trunk_capacity_l' => 20,
                'power' => 3000,
                'range_km' => 120,
                'top_speed_kmh' => 50,
                'weight_kg' => 100.00,
                'charge_time_h' => 5.50,
                'battery_capaciy' => 2.40,
                'max_charge_power_kw' => null,
                'battery_voltage_v' => 72.0,
                'is_battery_removable' => true,
                'warranty_months' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
