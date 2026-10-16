<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Specialty;
use App\Models\Procedure;
use App\Models\Subprocedure;
use App\Models\Doctor;

class DemoSpecialtiesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Limpiar data
        DB::table('appointments')->truncate();
        DB::table('subprocedures')->truncate();
        DB::table('procedures')->truncate();
        DB::table('specialties')->truncate();
        DB::table('doctor_specialty')->truncate();
        DB::table('appointment_procedure')->truncate();
        DB::table('appointment_subprocedure')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Especialidades Solo Consulta Base
        $medGeneral = Specialty::create([
            'name' => 'Medicina General',
            'description' => 'Atención primaria y consulta general.',
            'base_price' => 0.00,
            'is_active' => true,
        ]);

        $pediatria = Specialty::create([
            'name' => 'Pediatría Básica',
            'description' => 'Control de niño sano y consulta pediátrica general.',
            'base_price' => 30.00,
            'is_active' => true,
        ]);

        // 2. Especialidades con Procedimientos (sin subprocedimientos)
        $dermatologia = Specialty::create([
            'name' => 'Dermatología',
            'description' => 'Especialidad médica encargada del estudio, conocimiento y tratamiento de las enfermedades de la piel.',
            'base_price' => 40.00,
            'is_active' => true,
        ]);

        Procedure::create([
            'specialty_id' => $dermatologia->id,
            'name' => 'Crioterapia (Verrugas)',
            'description' => 'Eliminación de verrugas con nitrógeno líquido.',
            'price' => 25.00,
            'duration_minutes' => 15,
            'is_active' => true,
        ]);

        Procedure::create([
            'specialty_id' => $dermatologia->id,
            'name' => 'Biopsia de Piel',
            'description' => 'Extracción de tejido para estudio.',
            'price' => 60.00,
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        // 3. Especialidades con Procedimientos Y Subprocedimientos
        $odontologia = Specialty::create([
            'name' => 'Odontología Integral',
            'description' => 'Cuidado integral de la salud bucal.',
            'base_price' => 0.00,
            'is_active' => true,
        ]);

        $ortodoncia = Procedure::create([
            'specialty_id' => $odontologia->id,
            'name' => 'Tratamiento de Ortodoncia',
            'description' => 'Colocación y ajuste de brackets.',
            'price' => 0.00, // Precio a nivel de subprocedimiento
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        Subprocedure::create([
            'procedure_id' => $ortodoncia->id,
            'name' => 'Instalación de Brackets Metálicos',
            'price' => 250.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        Subprocedure::create([
            'procedure_id' => $ortodoncia->id,
            'name' => 'Instalación de Brackets Zafiro',
            'price' => 450.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        Subprocedure::create([
            'procedure_id' => $ortodoncia->id,
            'name' => 'Control Mensual Ortodoncia',
            'price' => 40.00,
            'duration_minutes' => 20,
            'is_active' => true,
        ]);

        $endodoncia = Procedure::create([
            'specialty_id' => $odontologia->id,
            'name' => 'Endodoncia (Tratamiento de Conducto)',
            'description' => 'Tratamiento de conducto para salvar piezas dentales.',
            'price' => 0.00, // Precio a nivel de subprocedimiento
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        Subprocedure::create([
            'procedure_id' => $endodoncia->id,
            'name' => 'Endodoncia Unirradicular',
            'price' => 120.00,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        Subprocedure::create([
            'procedure_id' => $endodoncia->id,
            'name' => 'Endodoncia Multirradicular',
            'price' => 180.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        // Asignar médicos existentes a las nuevas especialidades
        $doctors = Doctor::all();
        $specialtiesIds = [$medGeneral->id, $pediatria->id, $dermatologia->id, $odontologia->id];

        foreach ($doctors as $index => $doctor) {
            // Asignarle 1 o 2 especialidades a cada doctor
            $docSpecs = [];
            $docSpecs[] = $specialtiesIds[$index % count($specialtiesIds)];
            if ($index % 2 === 0) {
                $docSpecs[] = $specialtiesIds[($index + 1) % count($specialtiesIds)];
            }
            $doctor->specialties()->sync($docSpecs);
        }
    }
}
