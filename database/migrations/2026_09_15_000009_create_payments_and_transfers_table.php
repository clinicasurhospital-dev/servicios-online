<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->enum('payment_method', ['ONLINE', 'BANK_TRANSFER', 'CASH', 'CARD_ONSITE'])->default('ONLINE');
            $table->string('gateway_name')->nullable();
            $table->string('gateway_transaction_id')->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['PENDING', 'PAID', 'REJECTED', 'REFUNDED'])->default('PENDING');
            $table->dateTime('paid_at')->nullable();
            $table->json('payload_response')->nullable();
            $table->timestamps();

            $table->index(['appointment_id', 'status']);
        });

        Schema::create('bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('reference_number')->index();
            $table->string('receipt_path'); // Almacenado en storage privado
            $table->string('receipt_original_name');
            $table->decimal('amount', 10, 2);
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            
            $table->enum('validation_status', [
                'PENDING_VALIDATION',
                'APPROVED',
                'REJECTED',
            ])->default('PENDING_VALIDATION');

            $table->foreignId('validated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('validated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['validation_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transfers');
        Schema::dropIfExists('payments');
    }
};
