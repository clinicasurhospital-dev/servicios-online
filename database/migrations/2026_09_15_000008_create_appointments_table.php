<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. SUR-2026-1049
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained('specialties')->cascadeOnDelete();
            $table->foreignId('procedure_id')->nullable()->constrained('procedures')->nullOnDelete();
            
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            
            $table->enum('status', [
                'PENDING',
                'PENDING_PAYMENT',
                'CONFIRMED',
                'RESCHEDULED',
                'CANCELLED',
                'COMPLETED',
                'NO_SHOW',
            ])->default('PENDING');

            $table->decimal('price', 10, 2);
            $table->text('patient_notes')->nullable();
            $table->text('clinical_notes')->nullable();
            
            $table->text('cancelled_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('cancelled_at')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Critical concurrency and lookup indexes
            $table->index(['doctor_id', 'start_at', 'end_at'], 'idx_appointments_doctor_time');
            $table->index(['patient_id', 'start_at'], 'idx_appointments_patient_time');
            $table->index(['status', 'start_at'], 'idx_appointments_status_time');
        });

        Schema::create('appointment_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['appointment_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_status_histories');
        Schema::dropIfExists('appointments');
    }
};
