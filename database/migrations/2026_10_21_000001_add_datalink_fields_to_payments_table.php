<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('datalink_url', 500)->nullable()->after('gateway_transaction_id');
            $table->dateTime('datalink_sent_at')->nullable()->after('datalink_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['datalink_url', 'datalink_sent_at']);
        });
    }
};
