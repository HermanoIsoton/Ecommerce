<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table){
            $table->foreign('product_id')->constrained()->primary();
            $table->decimal('power_w', 8,2)->unsigned();
            $table->decimal('range_km', 5,2)->unsigned();
            $table->decimal('top_speed_kmh', 5,2)->unsigned();
            $table->decimal('weight_kg', 5,2)->unsigned();
            $table->decimal('charge_time_h', 4,2)->unsigned();
            $table->decimal('battery_capacity_wh', 8,2)->unsigned();
            $table->decimal('battery_voltage_v', 5,2)->unsigned();
            $table->boolean('is_battery_removable')->default('false');
           
        });
    }

  
    public function down(): void
    {
     Schema::dropIfExists('motorcycle_details');  
    }
};
