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
        Schema::create('coach_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patient_patients')->onDelete('cascade');
            $table->date('date');
            $table->text('message');
            $table->string('tip')->nullable();
            $table->string('icon')->default('lucide:flame');
            $table->string('mood')->default('encouraging');
            $table->string('source')->default('ai');
            $table->json('context_snapshot')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coach_messages');
    }
};
