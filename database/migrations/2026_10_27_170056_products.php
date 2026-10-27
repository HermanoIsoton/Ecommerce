<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('products', function (Blueprint $tabel){
            $table->id()->primary();
            $table->foreign_id('category_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_url');
            $table->decimal('price', 10,2)->unsigned();
            $table->int('stock')->unsigned();
            $table->boolean('is_active'); 
            $table->timestamp('deleted_at');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
