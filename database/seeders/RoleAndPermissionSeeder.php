<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_roles',
            'manage_specialties',
            'manage_procedures',
            'manage_doctors',
            'manage_schedules',
            'manage_patients',
            'view_all_appointments',
            'create_appointment_manual',
            'manage_payments',
            'validate_bank_transfers',
            'view_metrics',
            'view_audit_logs',
            'view_doctor_reviews',
            'manage_doctor_reviews',
            'view_doctor_own_agenda',
            'patient_portal_access',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'SUPER_ADMIN', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'ADMIN', 'guard_name' => 'web']);
        $reception = Role::firstOrCreate(['name' => 'RECEPTION', 'guard_name' => 'web']);
        $doctor = Role::firstOrCreate(['name' => 'DOCTOR', 'guard_name' => 'web']);
        $patient = Role::firstOrCreate(['name' => 'PATIENT', 'guard_name' => 'web']);

        // Assign Permissions
        $admin->syncPermissions([
            'view_dashboard', 'manage_users', 'manage_specialties', 'manage_procedures',
            'manage_doctors', 'manage_schedules', 'manage_patients', 'view_all_appointments',
            'create_appointment_manual', 'manage_payments', 'validate_bank_transfers',
            'view_metrics', 'view_audit_logs', 'view_doctor_reviews', 'manage_doctor_reviews',
        ]);

        $reception->syncPermissions([
            'view_dashboard', 'manage_patients', 'view_all_appointments',
            'create_appointment_manual', 'validate_bank_transfers', 'view_doctor_reviews',
        ]);

        $doctor->syncPermissions([
            'view_doctor_own_agenda',
        ]);

        $patient->syncPermissions([
            'patient_portal_access',
        ]);

        // Create Default Super Admin & Admin Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@surhospital.com'],
            [
                'name' => 'Administrador SurHospital',
                'phone' => '+593 99 123 4567',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $adminUser->assignRole('SUPER_ADMIN');

        $receptionUser = User::firstOrCreate(
            ['email' => 'recepcion@surhospital.com'],
            [
                'name' => 'Recepción SurHospital',
                'phone' => '+593 99 234 5678',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $receptionUser->assignRole('RECEPTION');
    }
}
