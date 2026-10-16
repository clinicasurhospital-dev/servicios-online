<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('license_number')->unique(); // Registro médico / CMP / MSP
            $table->string('primary_specialty_name')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('room_office')->nullable(); // Consultorio e.g. "Cons. 204 - Torre Médica"
            $table->decimal('default_consultation_price', 10, 2)->nullable();
            $table->unsignedInteger('default_slot_duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active']);
        });

        Schema::create('doctor_specialty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained('specialties')->cascadeOnDelete();
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->unsignedInteger('custom_duration_minutes')->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'specialty_id']);
        });

        Schema::create('doctor_procedure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('procedure_id')->constrained('procedures')->cascadeOnDelete();
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->unsignedInteger('custom_duration_minutes')->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'procedure_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_procedure');
        Schema::dropIfExists('doctor_specialty');
        Schema::dropIfExists('doctors');
    }
};
