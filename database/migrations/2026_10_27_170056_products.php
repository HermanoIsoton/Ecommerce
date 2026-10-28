<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table){
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_url');
            $table->decimal('price', 10,2)->unsigned();
            $table->decimal('stock')->unsigned();
            $table->boolean('is_active'); 
            $table->timestamp('deleted_at');
            // Gerais do Veículo
            $table->unsignedSmallInteger('model_year')->nullable();        
            $table->unsignedTinyInteger('seating_capacity')->nullable();   
            $table->unsignedSmallInteger('doors_count')->nullable();   
            $table->unsignedMediumInteger('trunk_capacity_l')->nullable();
            //details
            $table->unsignedInteger('power');
            $table->unsignedSmallInteger('range_km');
            $table->unsignedSmallInteger('top_speed_kmh');
            $table->decimal('weight_kg', 6,2);
            //BATTERY
            $table->decimal('charge_time_h', 4, 2);
            $table->decimal('battery_capaciy', 6, 2);
            $table->unsignedSmallInteger('max_charge_power_kw')->nullable();
            $table->decimal('battery_voltage_v', 5, 1);
            $table->boolean('is_battery_removable')->default(false);
            // Garantia
            $table->unsignedSmallInteger('warranty_months')->nullable();

           
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
