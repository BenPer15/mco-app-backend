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
        Schema::create('patient_patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('core_users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamp('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->integer('height_cm')->nullable();
            $table->string('surgery_type')->nullable();
            $table->timestamp('surgery_date')->nullable();
            $table->json('settings')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
