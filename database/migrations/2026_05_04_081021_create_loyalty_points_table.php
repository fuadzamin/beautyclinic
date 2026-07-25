<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('points_earned');
            $table->unsignedInteger('total_points');
            $table->enum('source', ['appointment', 'product_purchase']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
