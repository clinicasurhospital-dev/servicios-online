<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReception();
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->isAdmin() || $user->isReception()) {
            return true;
        }

        if ($user->isDoctor() && $user->doctor) {
            // A doctor can only view patients who have appointments with them
            return $patient->appointments()->where('doctor_id', $user->doctor->id)->exists();
        }

        if ($user->isPatient() && $user->patient) {
            return $patient->id === $user->patient->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isReception() || $user->isPatient();
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($user->isAdmin() || $user->isReception()) {
            return true;
        }

        if ($user->isPatient() && $user->patient) {
            return $patient->id === $user->patient->id;
        }

        return false;
    }
}
