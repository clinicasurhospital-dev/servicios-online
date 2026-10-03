<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Clinical Records (One-to-One with Patient)
        Schema::create('clinical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained('patients')->cascadeOnDelete();
            $table->string('record_number', 50)->unique();
            $table->string('blood_type', 10)->nullable();
            $table->boolean('organ_donor')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Patient Allergies
        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->cascadeOnDelete();
            $table->string('substance', 255);
            $table->string('allergy_type', 50)->default('OTHER'); // MEDICATION, FOOD, ENVIRONMENTAL, OTHER
            $table->string('severity', 50)->default('MODERATE'); // MILD, MODERATE, SEVERE, LIFE_THREATENING
            $table->string('reaction', 255)->nullable();
            $table->boolean('is_critical')->default(false); // Triggers prominent Clinical Alert
            $table->date('diagnosed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['clinical_record_id', 'is_critical']);
        });

        // 3. Patient Chronic Conditions
        Schema::create('patient_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->cascadeOnDelete();
            $table->string('condition_name', 255);
            $table->string('icd10_code', 20)->nullable();
            $table->string('status', 50)->default('ACTIVE'); // ACTIVE, CONTROLLED, REMISSION, RESOLVED
            $table->date('diagnosed_date')->nullable();
            $table->boolean('is_clinical_alert')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['clinical_record_id', 'status']);
        });

        // 4. Patient Habitual Medications
        Schema::create('patient_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->cascadeOnDelete();
            $table->string('drug_name', 255);
            $table->string('concentration', 100)->nullable();
            $table->string('dose', 100)->nullable();
            $table->string('frequency', 100)->nullable();
            $table->string('route', 50)->nullable(); // ORAL, SUBCUTANEOUS, INTRAVENOUS, etc.
            $table->string('status', 50)->default('ACTIVE'); // ACTIVE, SUSPENDED, DISCONTINUED
            $table->date('start_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['clinical_record_id', 'status']);
        });

        // 5. Patient Backgrounds (Personal, Family, Surgical, Habit, etc.)
        Schema::create('patient_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->cascadeOnDelete();
            $table->string('category', 50); // PERSONAL, FAMILY, SURGICAL, HABIT, GYNECO_OBSTETRIC, PEDIATRIC
            $table->string('title', 255);
            $table->string('relationship', 100)->nullable(); // For family backgrounds
            $table->string('event_year', 20)->nullable();
            $table->string('institution', 255)->nullable(); // For surgeries
            $table->text('details')->nullable();
            $table->boolean('is_relevant')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['clinical_record_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_backgrounds');
        Schema::dropIfExists('patient_medications');
        Schema::dropIfExists('patient_conditions');
        Schema::dropIfExists('patient_allergies');
        Schema::dropIfExists('clinical_records');
    }
};
