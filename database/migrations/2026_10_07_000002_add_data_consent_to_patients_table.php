<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'data_consent_accepted')) {
                $table->boolean('data_consent_accepted')->default(false)->after('medical_notes');
            }
            if (!Schema::hasColumn('patients', 'data_consent_accepted_at')) {
                $table->timestamp('data_consent_accepted_at')->nullable()->after('data_consent_accepted');
            }
            if (!Schema::hasColumn('patients', 'data_consent_ip')) {
                $table->string('data_consent_ip', 45)->nullable()->after('data_consent_accepted_at');
            }
            if (!Schema::hasColumn('patients', 'data_consent_user_agent')) {
                $table->text('data_consent_user_agent')->nullable()->after('data_consent_ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'data_consent_accepted',
                'data_consent_accepted_at',
                'data_consent_ip',
                'data_consent_user_agent',
            ]);
        });
    }
};
