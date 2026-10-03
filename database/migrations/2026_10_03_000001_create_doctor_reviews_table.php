<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            
            // Puntuaciones
            $table->unsignedTinyInteger('rating')->default(5); // 1 to 5 stars
            $table->unsignedTinyInteger('attention_rating')->nullable(); // 1 to 5 stars
            $table->unsignedTinyInteger('punctuality_rating')->nullable(); // 1 to 5 stars
            $table->unsignedTinyInteger('facilities_rating')->nullable(); // 1 to 5 stars
            $table->unsignedTinyInteger('recommendation_score')->nullable(); // 1 to 10
            
            // Sugerencia y comentarios
            $table->text('comment')->nullable();
            $table->boolean('is_anonymous')->default(false);
            
            // Canal de origen
            $table->string('channel')->default('PORTAL'); // PORTAL, WHATSAPP, EMAIL
            
            // Token para enlace directo (WhatsApp / Correo)
            $table->string('token', 64)->nullable()->unique();
            $table->dateTime('token_expires_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            
            // Moderación / Visibilidad
            $table->boolean('is_visible')->default(true);
            
            $table->timestamps();

            // Índices para optimizar consultas de métricas y filtrado
            $table->index(['doctor_id', 'is_visible', 'rating'], 'idx_doctor_reviews_rating');
            $table->index(['patient_id', 'created_at'], 'idx_doctor_reviews_patient');
            $table->index(['submitted_at'], 'idx_doctor_reviews_submitted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_reviews');
    }
};
