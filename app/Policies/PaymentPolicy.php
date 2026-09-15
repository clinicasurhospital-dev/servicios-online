<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReception() || $user->isPatient();
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->isAdmin() || $user->isReception()) {
            return true;
        }

        if ($user->isPatient() && $user->patient) {
            return $payment->appointment && $payment->appointment->patient_id === $user->patient->id;
        }

        return false;
    }

    public function validateTransfer(User $user): bool
    {
        return $user->isAdmin() || $user->isReception();
    }
}
