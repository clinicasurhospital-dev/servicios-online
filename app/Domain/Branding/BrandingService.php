<?php

namespace App\Domain\Branding;

class BrandingService
{
    /**
     * Get the client clinic name (e.g. SurHospital).
     */
    public function clientName(): string
    {
        return config('branding.client_name', 'SurHospital');
    }

    /**
     * Get the platform software name (e.g. Nexaris Link).
     */
    public function platformName(): string
    {
        return config('branding.platform_name', 'Nexaris Link');
    }

    /**
     * Get the page title for the given section.
     */
    public function title(string $section = 'public'): string
    {
        return match ($section) {
            'admin' => config('branding.display.admin_title', 'Nexaris Link | ' . $this->clientName()),
            'patient' => config('branding.display.patient_title', $this->clientName() . ' — Portal de Pacientes'),
            default => config('branding.display.public_title', $this->clientName() . ' — Especialistas en tu Salud'),
        };
    }

    /**
     * Get brand color by key.
     */
    public function color(string $key, string $default = '#0700FF'): string
    {
        return config("branding.colors.{$key}", $default);
    }

    /**
     * Get primary brand color.
     */
    public function primaryColor(): string
    {
        return $this->color('primary', '#0700FF');
    }

    /**
     * Get contact details.
     */
    public function contact(string $key = null): mixed
    {
        if ($key) {
            return config("branding.contact.{$key}", '');
        }

        return config('branding.contact', []);
    }

    /**
     * Get banking transfer details.
     */
    public function banking(string $key = null): mixed
    {
        if ($key) {
            return config("branding.banking.{$key}", '');
        }

        return config('branding.banking', []);
    }

    /**
     * Get the footer powered by text.
     */
    public function poweredBy(): string
    {
        return config('branding.display.powered_by_text', 'Desarrollado por Nexaris Link');
    }

    /**
     * Check if powered by footer should be shown.
     */
    public function showPoweredBy(): bool
    {
        return (bool) config('branding.display.show_powered_by', true);
    }

    /**
     * Minimum hours before appointment required to reschedule.
     */
    public function minimumRescheduleHours(): int
    {
        return (int) config('branding.display.minimum_reschedule_hours', 24);
    }

    /**
     * Determine if patient/public online booking is globally enabled.
     */
    public function isPatientBookingEnabled(): bool
    {
        return (bool) config('branding.enable_patient_booking', false);
    }

    /**
     * Determine if appointment booking button should be visible.
     * Always visible for Admin and Receptionist staff.
     * For patients and public visitors, controlled by ENABLE_PATIENT_BOOKING.
     */
    public function canShowBookingButton(?\App\Models\User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if ($user && ($user->hasRole('ADMIN') || $user->hasRole('RECEPTION'))) {
            return true;
        }

        return $this->isPatientBookingEnabled();
    }

    /**
     * Get any config value under branding.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return config("branding.{$key}", $default);
    }
}

