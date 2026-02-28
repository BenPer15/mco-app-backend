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
        Schema::create('gamification_patient_achievements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patient_patients')->onDelete('cascade');
            $table->foreignUuid('achievement_id')->constrained('gamification_achievements')->onDelete('cascade');
            $table->timestamp('unlocked_at');
            $table->timestamps();

            $table->unique(['patient_id', 'achievement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_patient_achievements');
    }
};
