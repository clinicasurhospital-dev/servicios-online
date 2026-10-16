<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('clinical_history_path')->nullable()->after('clinical_notes');
            $table->string('clinical_history_filename')->nullable()->after('clinical_history_path');
            $table->string('procedure_document_path')->nullable()->after('clinical_history_filename');
            $table->string('procedure_document_filename')->nullable()->after('procedure_document_path');
            $table->dateTime('clinical_history_uploaded_at')->nullable()->after('procedure_document_filename');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'clinical_history_path',
                'clinical_history_filename',
                'procedure_document_path',
                'procedure_document_filename',
                'clinical_history_uploaded_at',
            ]);
        });
    }
};
