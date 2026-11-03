<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Patinete Urban Sprint',
                'slug' => 'patinete-urban-sprint',
                'description' => 'Patinete elétrico leve e dobrável, ideal para trajetos curtos na cidade.',
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSm-FmZ7qzgdkElfgQjg5QXQg13CKLr-r6x_V7ULcwkdA&s=10',
                'price' => 2890.00,
                'stock' => 12,
                'is_active' => true,
                'model_year' => 2026,
                'seating_capacity' => 1,
                'doors_count' => 0,
                'trunk_capacity_l' => null,
                'type' => 'autopropelido',
                'power' => 350,
                'range_km' => 25,
                'top_speed_kmh' => 25,
                'weight_kg' => 14.50,
                'charge_time_h' => 4.00,
                'battery_capaciy' => 280.00,
                'max_charge_power_kw' => null,
                'battery_voltage_v' => 36.0,
                'is_battery_removable' => false,
                'warranty_months' => 12
            ],
            [
                'category_id' => 2,
                'name' => 'Bike Elétrica Trilha 500',
                'slug' => 'bike-eletrica-trilha-500',
                'description' => 'Bicicleta elétrica com quadro em alumínio e bateria removível para recarga em casa.',
                'image_url' => 'https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcRfpZW7UIcHN8N3vJIDbsNpwwzpNiGfXqYByYdAsNqiubV0pjtzAz3FHuOrL3RDoBRKirH8fPBqAVuPoR2FVCAa0D3Y_vcANCRY7AnmUyqyeyaWT8ZDy4gO&usqp=CAc.jpeg',
                'price' => 6490.00,
                'stock' => 5,
                'is_active' => true,
                'model_year' => 2026,
                'seating_capacity' => 1,
                'doors_count' => 0,
                'trunk_capacity_l' => null,
                'type' => 'autopropelido',
                'power' => 500,
                'range_km' => 60,
                'top_speed_kmh' => 32,
                'weight_kg' => 23.00,
                'charge_time_h' => 5.00,
                'battery_capaciy' => 624.00,
                'max_charge_power_kw' => null,
                'battery_voltage_v' => 48.0,
                'is_battery_removable' => true,
                'warranty_months' => 12
            ],
            [
                'category_id' => 3,
                'name' => 'Moto Elétrica E-Moto',
                'slug' => 'moto-eletrica-e-moto',
                'description' => 'Moto elétrica para deslocamentos urbanos, com dois lugares e bateria removível.',
                'image_url' => 'https://cdn.awsli.com.br/600x450/2299/2299072/produto/144098877/whatsapp-image-2025-08-19-at-10-59-31-tdspbsxr9r.jpeg',
                'price' => 15990.00,
                'stock' => 7,
                'is_active' => true,
                'model_year' => 2026,
                'seating_capacity' => 2,
                'doors_count' => 0,
                'trunk_capacity_l' => 20,
                'type' => 'ciclomotor',
                'power' => 3000,
                'range_km' => 120,
                'top_speed_kmh' => 50,
                'weight_kg' => 100.00,
                'charge_time_h' => 5.50,
                'battery_capaciy' => 2.40,
                'max_charge_power_kw' => null,
                'battery_voltage_v' => 72.0,
                'is_battery_removable' => true,
                'warranty_months' => 12
            ],
        ];
        foreach($products as $product){
            Product::create($product);
        }
    }
}
