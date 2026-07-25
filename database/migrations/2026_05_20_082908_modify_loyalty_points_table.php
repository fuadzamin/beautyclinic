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
        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->integer('points_earned')->change();
            $table->string('source')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->unsignedInteger('points_earned')->change();
            $table->enum('source', ['appointment', 'product_purchase'])->change();
        });
    }
};
