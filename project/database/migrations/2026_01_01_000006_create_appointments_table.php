<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('type', ['opd', 'ipd', 'emergency', 'follow_up']);
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->unsignedBigInteger('booked_by')->nullable();
            $table->foreign('booked_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index('appointment_date');
            $table->index('doctor_id');
            $table->index('patient_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
