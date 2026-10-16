<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isReception() || $user->isDoctor() || $user->isPatient();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isReception()) {
            return true;
        }

        if ($user->isDoctor() && $user->doctor) {
            return $appointment->doctor_id === $user->doctor->id;
        }

        if ($user->isPatient() && $user->patient) {
            return $appointment->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isReception() || $user->isPatient();
    }

    /**
     * Determine whether the user can update/reschedule the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isReception()) {
            return true;
        }

        if ($user->isPatient() && $user->patient) {
            return $appointment->patient_id === $user->patient->id && $appointment->canBeRescheduled();
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isReception()) {
            return true;
        }

        if ($user->isDoctor() && $user->doctor) {
            return $appointment->doctor_id === $user->doctor->id && $appointment->canBeCancelled();
        }

        if ($user->isPatient() && $user->patient) {
            return $appointment->patient_id === $user->patient->id && $appointment->canBeCancelled();
        }

        return false;
    }

    /**
     * Determine whether the user can complete the appointment (Doctor / Admin only).
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor() && $user->doctor) {
            return $appointment->doctor_id === $user->doctor->id;
        }

        return false;
    }
}
