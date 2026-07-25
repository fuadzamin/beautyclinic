<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branch_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('stock_quantity')->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'product_id']);
        });

        // Migrate existing stock to branch 1 (Pusat)
        $products = DB::table('products')->get();
        $branch = DB::table('branches')->first(); // Get Pusat

        if ($branch) {
            foreach ($products as $product) {
                DB::table('branch_product')->insert([
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'stock_quantity' => $product->stock_quantity ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Remove stock_quantity from products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0);
        });

        // Re-migrate stock back (we'll just take the sum)
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $totalStock = DB::table('branch_product')
                ->where('product_id', $product->id)
                ->sum('stock_quantity');
            DB::table('products')
                ->where('id', $product->id)
                ->update(['stock_quantity' => $totalStock]);
        }

        Schema::dropIfExists('branch_product');
    }
};
