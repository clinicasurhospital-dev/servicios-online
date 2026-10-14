<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Ensure all Roles exist
        $roles = [
            'SUPER_ADMIN',
            'ADMIN',
            'RECEPTION',
            'DOCTOR',
            'SPECIALIST',
            'PATIENT',
        ];

        $roleModels = [];
        foreach ($roles as $roleName) {
            $roleModels[$roleName] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Define all Permissions by Module
        $permissions = [
            // Dashboard
            'view_dashboard',
            'view_metrics',

            // Usuarios & Seguridad
            'manage_users',
            'view_users',
            'manage_roles',
            'view_roles',

            // Pacientes
            'manage_patients',
            'view_patients',

            // Médicos y Horarios
            'manage_doctors',
            'manage_schedules',

            // Especialidades y Procedimientos
            'manage_specialties',
            'manage_procedures',

            // Citas y Agenda
            'view_all_appointments',
            'create_appointment_manual',
            'manage_appointments',

            // Pagos y Transferencias
            'manage_payments',
            'validate_bank_transfers',

            // Historia Clínica y Plantillas
            'clinical_template_view',
            'clinical_template_manage',
            'view_clinical_records',
            'manage_clinical_encounters',

            // Valoraciones y Sugerencias
            'view_doctor_reviews',
            'manage_doctor_reviews',

            // Auditoría
            'view_audit_logs',

            // Médico / Especialista específico
            'view_doctor_own_agenda',

            // Paciente Portal
            'patient_portal_access',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 3. Assign Permissions
        // Admin
        $roleModels['ADMIN']->givePermissionTo([
            'view_dashboard', 'view_metrics',
            'manage_users', 'view_users',
            'manage_roles', 'view_roles',
            'manage_patients', 'view_patients',
            'manage_doctors', 'manage_schedules',
            'manage_specialties', 'manage_procedures',
            'view_all_appointments', 'create_appointment_manual', 'manage_appointments',
            'manage_payments', 'validate_bank_transfers',
            'clinical_template_view', 'clinical_template_manage',
            'view_clinical_records',
            'view_doctor_reviews', 'manage_doctor_reviews',
            'view_audit_logs',
        ]);

        // Recepcionista
        $roleModels['RECEPTION']->givePermissionTo([
            'view_dashboard',
            'manage_patients', 'view_patients',
            'view_all_appointments', 'create_appointment_manual',
            'validate_bank_transfers',
            'view_doctor_reviews',
        ]);

        // Médico (General)
        $roleModels['DOCTOR']->givePermissionTo([
            'view_dashboard',
            'view_doctor_own_agenda',
            'view_clinical_records',
            'manage_clinical_encounters',
            'view_doctor_reviews',
        ]);

        // Especialista
        $roleModels['SPECIALIST']->givePermissionTo([
            'view_dashboard',
            'view_doctor_own_agenda',
            'view_clinical_records',
            'manage_clinical_encounters',
            'view_doctor_reviews',
        ]);

        // Paciente
        $roleModels['PATIENT']->givePermissionTo([
            'patient_portal_access',
        ]);
    }

    public function down(): void
    {
        // Keep roles/permissions for data integrity or drop specialist if needed
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
