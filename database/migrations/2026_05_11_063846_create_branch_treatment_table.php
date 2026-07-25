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
        Schema::create('branch_treatment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'treatment_id']);
        });

        // Migrate existing treatments to Branch 1 (if any exist)
        $branch = DB::table('branches')->first();
        if ($branch) {
            $treatments = DB::table('treatments')->get();
            foreach ($treatments as $treatment) {
                DB::table('branch_treatment')->insert([
                    'branch_id'    => $branch->id,
                    'treatment_id' => $treatment->id,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_treatment');
    }
};
