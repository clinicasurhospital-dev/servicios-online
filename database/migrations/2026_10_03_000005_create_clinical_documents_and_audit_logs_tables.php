<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Clinical Documents (Private Encrypted/Protected Storage)
        Schema::create('clinical_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_record_id')->constrained('clinical_records')->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('clinical_encounters')->nullOnDelete();
            $table->string('document_type', 50)->default('OTHER'); // LAB_REPORT, IMAGING, SURGICAL_REPORT, CONSENT_FORM, EXTERNAL_RECORD, OTHER
            $table->string('file_path', 500); // Path in private disk (medical_records)
            $table->string('file_name', 255);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type', 100);
            $table->text('description')->nullable();
            $table->boolean('is_sensitive')->default(false);
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['clinical_record_id', 'document_type']);
            $table->index(['encounter_id']);
        });

        // 2. Clinical Audit Logs (High-fidelity HIPAA/GDPR style clinical audit trail)
        Schema::create('clinical_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 150)->nullable();
            $table->string('role', 50)->nullable();
            $table->string('event', 100); // EncounterCreated, EncounterFinalized, AddendumCreated, TemplatePublished, etc.
            $table->string('auditable_type', 150);
            $table->unsignedBigInteger('auditable_id');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_audit_logs');
        Schema::dropIfExists('clinical_documents');
    }
};
