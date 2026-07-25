<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->unique()->nullable()->after('name');
            $table->text('address')->nullable()->after('phone');
            $table->enum('role', ['customer'])->default('customer')->after('address');
            $table->unsignedInteger('loyalty_points')->default(0)->after('role');
            $table->timestamp('last_login')->nullable()->after('loyalty_points');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'role', 'loyalty_points', 'last_login']);
        });
    }
};
