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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE staff MODIFY COLUMN role ENUM('owner', 'branch_manager', 'admin_klinik', 'admin_produk') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE staff MODIFY COLUMN role ENUM('owner', 'admin_klinik', 'admin_produk') NOT NULL");
    }
};
