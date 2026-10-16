<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('specialties', function (Blueprint $table) {
            $table->boolean('has_procedures')->default(false)->after('description');
            $table->decimal('base_price', 10, 2)->nullable()->change();
        });

        Schema::table('procedures', function (Blueprint $table) {
            $table->boolean('has_subprocedures')->default(false)->after('description');
            $table->decimal('price', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specialties', function (Blueprint $table) {
            $table->dropColumn('has_procedures');
            $table->decimal('base_price', 10, 2)->nullable(false)->change();
        });

        Schema::table('procedures', function (Blueprint $table) {
            $table->dropColumn('has_subprocedures');
            $table->decimal('price', 10, 2)->nullable(false)->change();
        });
    }
};
