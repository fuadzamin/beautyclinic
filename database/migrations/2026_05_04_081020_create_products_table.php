<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->enum('category', [
                'serum', 'sunscreen', 'moisturizer', 'cleanser',
                'acne_treatment', 'mask', 'body_care', 'soap'
            ]);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('image_url')->nullable();
            $table->text('ingredients')->nullable();
            $table->string('volume')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
            $table->index('stock_quantity');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
