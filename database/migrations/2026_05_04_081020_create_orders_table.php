<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('total_price');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'cash_on_delivery'])->default('pending');
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->text('notes')->nullable();
            $table->dateTime('order_date');
            $table->dateTime('pickup_date')->nullable();
            $table->timestamps();

            $table->index('order_number');
            $table->index('status');
            $table->index('customer_phone');
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
