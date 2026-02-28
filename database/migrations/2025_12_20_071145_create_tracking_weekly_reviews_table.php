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
        Schema::create('tracking_weekly_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patient_patients')->onDelete('cascade');

            $table->year('year');
            $table->unsignedTinyInteger('week');

            // Notes
            $table->unsignedTinyInteger('physical_score')->default(0);
            $table->unsignedTinyInteger('mental_score')->default(0);
            $table->unsignedTinyInteger('adherence_score')->default(0);

            $table->text('comment')->nullable();

            $table->string('source');
            $table->string('external_id')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'year', 'week'], 'unique_patient_weekly_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_weekly_reviews');
    }
};
