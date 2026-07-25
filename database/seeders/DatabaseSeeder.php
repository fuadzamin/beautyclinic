<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Treatment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Branches
        $branchPusat = \App\Models\Branch::firstOrCreate(
            ['name' => 'Aura Beauty - Pusat Jakarta'],
            [
                'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
                'phone'   => '021-1234567',
                'is_active' => true,
            ]
        );

        $branchBandung = \App\Models\Branch::firstOrCreate(
            ['name' => 'Aura Beauty - Cabang Bandung'],
            [
                'address' => 'Jl. Dago No. 88, Bandung',
                'phone'   => '022-7654321',
                'is_active' => true,
            ]
        );

        // Create Admin Staff
        Staff::firstOrCreate(
            ['email' => 'owner@clinic.com'],
            [
                'name'     => 'Owner Admin',
                'password' => 'password123',
                'role'     => 'owner',
                'phone'    => '6281234567890',
                'branch_id'=> null, // Owner has access to all
                'is_active'=> true,
            ]
        );

        Staff::firstOrCreate(
            ['email' => 'klinik@clinic.com'],
            [
                'name'     => 'Admin Klinik Jakarta',
                'password' => 'password123',
                'role'     => 'admin_klinik',
                'phone'    => '6281234567891',
                'branch_id'=> $branchPusat->id,
                'is_active'=> true,
            ]
        );

        Staff::firstOrCreate(
            ['email' => 'produk@clinic.com'],
            [
                'name'     => 'Admin Produk Bandung',
                'password' => 'password123',
                'role'     => 'admin_produk',
                'phone'    => '6281234567892',
                'branch_id'=> $branchBandung->id,
                'is_active'=> true,
            ]
        );

        // Create Default Customer
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name'     => 'Jane Doe',
                'password' => Hash::make('password123'),
                'phone'    => '6281234567893',
                'address'  => 'Jl. Kebumen Raya No. 1',
                'role'     => 'customer',
            ]
        );

        // Seed Treatments
        $treatments = [
            [
                'name' => 'Acne Peeling Treatment',
                'description' => 'Gentle chemical peel to clear acne and prevent breakouts.',
                'benefits' => 'Reduces acne, minimizes pores',
                'category' => 'facial',
                'price' => 250000,
                'duration_minutes' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Hydrating Glow Facial',
                'description' => 'Deep hydration facial using hyaluronic acid.',
                'benefits' => 'Deep moisturizing, glowing skin',
                'category' => 'facial',
                'price' => 300000,
                'duration_minutes' => 90,
                'image_url' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Laser Hair Removal',
                'description' => 'Permanent hair reduction using IPL laser.',
                'benefits' => 'Smooth skin, permanent hair loss',
                'category' => 'laser',
                'price' => 500000,
                'duration_minutes' => 45,
                'image_url' => 'https://images.unsplash.com/photo-1560750588-73207b1ef5b8?auto=format&fit=crop&w=500&q=80',
            ],
        ];

        foreach ($treatments as $treatment) {
            Treatment::firstOrCreate(['name' => $treatment['name']], $treatment);
        }

        // Seed Products
        $productData = [
            [
                'name' => 'Brightening Vita-C Serum',
                'description' => '10% Vitamin C serum for glowing skin.',
                'price' => 150000,
                'category' => 'serum',
                'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Daily Defense Sunscreen SPF 50',
                'description' => 'Lightweight, non-sticky sunscreen.',
                'price' => 95000,
                'category' => 'sunscreen',
                'image_url' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Centella Soothing Moisturizer',
                'description' => 'Calms redness and repairs skin barrier.',
                'price' => 120000,
                'category' => 'moisturizer',
                'image_url' => 'https://images.unsplash.com/photo-1608248593842-8021c6a85ce0?auto=format&fit=crop&w=500&q=80',
            ],
        ];

        $stockByBranch = [
            $branchPusat->id => [50, 100, 30],
            $branchBandung->id => [30, 75, 15],
        ];

        foreach ($productData as $i => $data) {
            $product = Product::firstOrCreate(['name' => $data['name']], $data);
            foreach ($stockByBranch as $branchId => $stocks) {
                DB::table('branch_product')->updateOrInsert(
                    ['branch_id' => $branchId, 'product_id' => $product->id],
                    ['stock_quantity' => $stocks[$i], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
