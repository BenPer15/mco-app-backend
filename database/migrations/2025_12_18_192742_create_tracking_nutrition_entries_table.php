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
        Schema::create('tracking_nutrition_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_day_id')->constrained('tracking_patient_days')->onDelete('cascade');
            $table->boolean('proteins_ok')->default(false);
            $table->boolean('vegetables_ok')->default(false);
            $table->boolean('hydration_ok')->default(false);
            $table->boolean('texture_ok')->default(false);
            $table->timestamp('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_nutrition_entries');
    }
};
