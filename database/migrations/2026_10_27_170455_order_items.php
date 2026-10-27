<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
         Schema::create('order_items', function (Blueprint $table){
          $table->id();
          $table->foreignId('order_id')->constrained();  
          $table->foreignId('product_id')->constrained();        
          $table->string('product_name');
          $table->decimal('unit_price', 10,2)->unsigned();       
          $table->decimal('quantity');
          $table->decimal('subtotal_cents', 10,2)->unsigned();            
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('order_items');  
    }
};
