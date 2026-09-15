<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedule_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('reason');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['doctor_id', 'start_datetime', 'end_datetime']);
        });

        Schema::create('doctor_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->enum('type', ['VACATION', 'MEDICAL_LEAVE', 'CONGRESS', 'PERSONAL', 'OTHER'])->default('VACATION');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['doctor_id', 'start_date', 'end_date', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_absences');
        Schema::dropIfExists('doctor_schedule_blocks');
    }
};
