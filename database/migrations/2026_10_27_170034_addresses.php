<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table){
            $table->id()->primary();
            $table->foreign_id('user_id')->constrained();
            $table->string('recipient');
            $table->string('cep');
            $table->string('street');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('district');
            $table->string('city');
            $table->char('state', 2);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
