<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('clinical_addendums');
        Schema::dropIfExists('clinical_diagnoses');
        Schema::dropIfExists('clinical_encounter_values');
        Schema::dropIfExists('clinical_encounters');
        Schema::enableForeignKeyConstraints();

        // 1. Clinical Encounters (Medical Attention Instance)
        Schema::create('clinical_encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('specialty_id')->constrained('specialties')->cascadeOnDelete();
            $table->foreignId('template_version_id')->constrained('clinical_template_versions')->cascadeOnDelete();
            $table->string('status', 30)->default('DRAFT'); // DRAFT, IN_PROGRESS, FINALIZED, AMENDED, CANCELLED
            $table->string('consultation_type', 30)->default('INITIAL'); // INITIAL, FOLLOW_UP, CONTROL, EMERGENCY, OTHER
            $table->text('reason_for_consultation')->nullable();
            $table->json('vital_signs_summary')->nullable(); // Snapshot of key vitals (weight, height, bp, bmi, hr, temp)
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'created_at']);
            $table->index(['doctor_id', 'created_at']);
            $table->index(['appointment_id']);
            $table->index(['status']);
        });

        // 2. Clinical Encounter Values (Typed Dynamic Field Responses)
        Schema::create('clinical_encounter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained('clinical_encounters')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('clinical_template_fields')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('clinical_template_sections')->cascadeOnDelete();
            $table->unsignedInteger('repeatable_index')->default(0);
            $table->longText('value_text')->nullable();
            $table->decimal('value_number', 12, 4)->nullable();
            $table->date('value_date')->nullable();
            $table->dateTime('value_datetime')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->json('value_json')->nullable(); // For multiselects, arrays, structured widgets
            $table->timestamps();

            $table->index(['encounter_id', 'field_id', 'repeatable_index'], 'idx_enc_field_rep');
            $table->index(['field_id']);
        });

        // 3. Clinical Diagnoses
        Schema::create('clinical_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained('clinical_encounters')->cascadeOnDelete();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->cascadeOnDelete();
            $table->string('code', 20); // CIE-10 code (e.g. I10, E11.9, J00)
            $table->string('description', 255);
            $table->string('type', 30)->default('PRIMARY'); // PRIMARY, SECONDARY, PRESUMPTIVE, CONFIRMED
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['encounter_id', 'type']);
            $table->index(['clinical_record_id', 'code']);
        });

        // 4. Clinical Addendums (Immutable post-finalization annotations)
        Schema::create('clinical_addendums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained('clinical_encounters')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 255);
            $table->longText('content');
            $table->timestamps();

            $table->index(['encounter_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_addendums');
        Schema::dropIfExists('clinical_diagnoses');
        Schema::dropIfExists('clinical_encounter_values');
        Schema::dropIfExists('clinical_encounters');
    }
};
