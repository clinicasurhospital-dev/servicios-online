<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SUPER_ADMIN');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('ADMIN') || $this->isSuperAdmin();
    }

    public function isReception(): bool
    {
        return $this->hasRole('RECEPTION') || $this->isAdmin();
    }

    public function isDoctor(): bool
    {
        return $this->hasRole('DOCTOR') || $this->hasRole('SPECIALIST');
    }

    public function isSpecialist(): bool
    {
        return $this->hasRole('SPECIALIST');
    }

    public function isPatient(): bool
    {
        return $this->hasRole('PATIENT');
    }

    /**
     * Get the primary role name in Spanish for display.
     */
    public function getRoleNameAttribute(): string
    {
        $role = $this->roles->first()?->name;

        return match ($role) {
            'SUPER_ADMIN' => 'Super Administrador',
            'ADMIN' => 'Administrador',
            'RECEPTION' => 'Recepcionista',
            'DOCTOR' => 'Médico',
            'SPECIALIST' => 'Especialista',
            'PATIENT' => 'Paciente',
            default => 'Usuario',
        };
    }

    /**
     * Get the badge color classes for the user's role.
     */
    public function getRoleBadgeClassesAttribute(): string
    {
        $role = $this->roles->first()?->name;

        return match ($role) {
            'SUPER_ADMIN' => 'bg-purple-50 text-purple-700 border-purple-200',
            'ADMIN' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'RECEPTION' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'DOCTOR' => 'bg-blue-50 text-blue-700 border-blue-200',
            'SPECIALIST' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            'PATIENT' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
