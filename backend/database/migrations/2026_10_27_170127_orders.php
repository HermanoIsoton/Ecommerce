<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['pendente', 'pago', 'enviado', 'entregue', 'cancelado'])->default('pendente');
            $table->decimal('subtotal_cents', 10,2)->unsigned();
            $table->decimal('shipping_cents', 10,2)->unsigned();
            $table->decimal('total_cents', 10,2)->unsigned();
            $table->string('ship_recipient');
            $table->string('ship_cep');
            $table->string('ship_street');
            $table->string('ship_number', 20);
            $table->string('ship_complement', 20);
            $table->string('ship_district');
            $table->string('ship_city');
            $table->char('ship_state', 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
