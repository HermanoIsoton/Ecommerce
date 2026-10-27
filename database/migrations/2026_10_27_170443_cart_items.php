<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('carts_items', function (Blueprint $table){
          $table->id()->primary();
          $table->foreign('cart_id')->constrained();  
          $table->foreign('product_id')->constrained();        
          $table->int('quantity')->unsigned();       
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('carts_items');  
    }
};
