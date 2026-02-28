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
        Schema::create('tracking_activity_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_day_id')->constrained('tracking_patient_days')->onDelete('cascade');
            $table->string('activity_type');
            $table->integer('duration_minutes');
            $table->string('intensity_level');
            $table->integer('borg_rating')->nullable();
            $table->text('notes')->nullable();
            $table->integer('number_of_steps')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_activity_entries');
    }
};
