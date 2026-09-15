<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;

class DoctorPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Medical directory is accessible to all authenticated roles
    }

    public function view(User $user, Doctor $doctor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Doctor $doctor): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor() && $user->doctor) {
            return $doctor->id === $user->doctor->id;
        }

        return false;
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin();
    }
}
