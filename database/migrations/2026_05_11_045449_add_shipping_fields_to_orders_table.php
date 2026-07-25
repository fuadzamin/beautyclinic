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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_method', ['pickup', 'shipping'])->default('pickup')->after('status');
            $table->text('shipping_address')->nullable()->after('delivery_method');
            $table->foreignId('fulfilled_by_branch_id')->nullable()->constrained('branches')->nullOnDelete()->after('shipping_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['fulfilled_by_branch_id']);
            $table->dropColumn(['delivery_method', 'shipping_address', 'fulfilled_by_branch_id']);
        });
    }
};
