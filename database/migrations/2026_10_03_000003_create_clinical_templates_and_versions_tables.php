<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('clinical_field_options');
        Schema::dropIfExists('clinical_template_fields');
        Schema::dropIfExists('clinical_template_sections');
        Schema::dropIfExists('clinical_template_versions');
        Schema::dropIfExists('clinical_templates');
        Schema::enableForeignKeyConstraints();

        // 1. Clinical Templates (Header)
        Schema::create('clinical_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Clinical Template Versions (Immutable when PUBLISHED)
        Schema::create('clinical_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_template_id')->constrained('clinical_templates')->cascadeOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->string('status', 30)->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->text('changelog')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['clinical_template_id', 'version_number'], 'ctv_template_ver_unique');
            $table->index(['clinical_template_id', 'status'], 'ctv_template_status_idx');
        });

        // 3. Clinical Template Sections
        Schema::create('clinical_template_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_version_id')->constrained('clinical_template_versions')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_repeatable')->default(false);
            $table->string('visibility', 30)->default('ALL'); // ALL, DOCTOR_ONLY, MEDICAL_STAFF
            $table->timestamps();

            $table->index(['template_version_id', 'order']);
        });

        // 4. Clinical Template Fields
        Schema::create('clinical_template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('clinical_template_sections')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 100); // e.g. sys_bp, dia_bp, heart_rate, allergy_note
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->string('field_type', 50); // TEXT, TEXTAREA, INTEGER, DECIMAL, DATE, DATETIME, TIME, BOOLEAN, SELECT, MULTISELECT, RADIO, CHECKBOX, FILE, IMAGE, DIAGNOSIS, MEDICATION, CLINICAL_CATALOG
            $table->string('placeholder', 255)->nullable();
            $table->text('default_value')->nullable();
            $table->string('unit', 50)->nullable(); // mmHg, kg, °C, bpm, cm, etc.
            $table->json('validation_rules')->nullable(); // min, max, min_length, max_length, regex, etc.
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_repeatable')->default(false);
            $table->boolean('is_sensitive')->default(false);
            $table->boolean('is_clinical_alert')->default(false);
            $table->boolean('is_patient_visible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('help_text', 255)->nullable();
            $table->timestamps();

            $table->index(['section_id', 'order']);
            $table->index(['code']);
        });

        // 5. Clinical Field Options (for SELECT, RADIO, MULTISELECT, CHECKBOX)
        Schema::create('clinical_field_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('clinical_template_fields')->cascadeOnDelete();
            $table->string('label', 255);
            $table->string('value', 255);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['field_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_field_options');
        Schema::dropIfExists('clinical_template_fields');
        Schema::dropIfExists('clinical_template_sections');
        Schema::dropIfExists('clinical_template_versions');
        Schema::dropIfExists('clinical_templates');
    }
};
