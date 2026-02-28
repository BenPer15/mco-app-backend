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
        Schema::create('tracking_step_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_day_id')->constrained('tracking_patient_days')->onDelete('cascade');
            $table->integer('steps');
            $table->string('source');
            $table->string('external_id')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_step_entries');
    }
};
