<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->enum('role', ['owner', 'admin_klinik', 'admin_produk']);
            $table->boolean('is_active')->default(true);
            $table->boolean('two_fa_enabled')->default(false);
            $table->string('two_fa_secret')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('role');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
