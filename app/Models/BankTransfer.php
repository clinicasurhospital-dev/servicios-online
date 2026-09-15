<?php

namespace App\Models;

use App\Domain\Payments\Enums\BankTransferStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'bank_name',
        'reference_number',
        'receipt_path',
        'receipt_original_name',
        'amount',
        'transfer_date',
        'notes',
        'validation_status',
        'validated_by_user_id',
        'validated_at',
        'rejection_reason',
    ];

    protected $casts = [
        'validation_status' => BankTransferStatus::class,
        'amount' => 'decimal:2',
        'transfer_date' => 'date',
        'validated_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->validation_status === BankTransferStatus::PENDING_VALIDATION;
    }

    public function isApproved(): bool
    {
        return $this->validation_status === BankTransferStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->validation_status === BankTransferStatus::REJECTED;
    }
}
