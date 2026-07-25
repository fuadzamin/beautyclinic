<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // e.g. TRX-20260511-0001
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained()->nullOnDelete();

            // Customer
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();

            // Financials
            $table->unsignedBigInteger('subtotal')->default(0);   // treatment
            $table->unsignedBigInteger('products_total')->default(0); // extra products
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);

            // Payment
            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'card'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('paid');
            $table->unsignedBigInteger('amount_paid')->default(0);
            $table->unsignedBigInteger('change_amount')->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'created_at']);
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
