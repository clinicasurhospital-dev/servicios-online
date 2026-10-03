<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            SpecialtyAndProcedureSeeder::class,
            DoctorAndScheduleSeeder::class,
            PatientAndAppointmentSeeder::class,
            ClinicalModuleSeeder::class,
        ]);
    }
}
