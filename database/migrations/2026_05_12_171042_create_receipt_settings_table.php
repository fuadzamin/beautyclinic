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
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            // Header
            $table->string('clinic_name')->default('AURA Beauty Clinic');
            $table->string('tagline')->nullable()->default('Kecantikan adalah investasi terbaik');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_url')->nullable();

            // Body display toggles
            $table->boolean('show_treatment')->default(true);
            $table->boolean('show_products')->default(true);
            $table->boolean('show_discount')->default(true);
            $table->boolean('show_payment_method')->default(true);
            $table->boolean('show_cashier_name')->default(false);
            $table->boolean('show_appointment_date')->default(true);

            // Footer
            $table->string('footer_message', 500)->nullable()->default('Terima kasih telah mempercayakan kecantikan Anda kepada kami 💖');
            $table->string('social_instagram')->nullable();
            $table->string('social_whatsapp')->nullable();
            $table->string('website')->nullable();

            // Auto-print setting
            $table->boolean('auto_print')->default(false);

            $table->timestamps();

            $table->unique('branch_id'); // one setting per branch (null = global default)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};
