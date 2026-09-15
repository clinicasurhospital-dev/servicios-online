<?php

namespace Database\Seeders;

use App\Models\Procedure;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtyAndProcedureSeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Cardiología',
                'slug' => 'cardiologia',
                'description' => 'Diagnóstico, tratamiento y prevención de enfermedades cardiovasculares y del corazón.',
                'icon' => 'heart',
                'base_price' => 45.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Electrocardiograma (ECG)', 'price' => 30.00, 'duration_minutes' => 20, 'description' => 'Evaluación del ritmo cardíaco y actividad eléctrica del corazón.'],
                    ['name' => 'Ecocardiograma Doppler', 'price' => 75.00, 'duration_minutes' => 40, 'description' => 'Ultrasonido detallado para evaluar estructuras y flujo sanguíneo cardíaco.'],
                    ['name' => 'Prueba de Esfuerzo (Ergometría)', 'price' => 60.00, 'duration_minutes' => 45, 'description' => 'Monitoreo de la función cardíaca durante el ejercicio físico.'],
                ],
            ],
            [
                'name' => 'Pediatría',
                'slug' => 'pediatria',
                'description' => 'Atención médica integral desde recién nacidos hasta adolescentes con calidez y enfoque preventivo.',
                'icon' => 'baby',
                'base_price' => 35.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Control de Niño Sano', 'price' => 35.00, 'duration_minutes' => 30, 'description' => 'Evaluación periódica de crecimiento, nutrición y desarrollo psicomotor.'],
                    ['name' => 'Aplicación de Esquema de Vacunación', 'price' => 25.00, 'duration_minutes' => 20, 'description' => 'Administración segura de vacunas pediátricas oficiales.'],
                ],
            ],
            [
                'name' => 'Dermatología',
                'slug' => 'dermatologia',
                'description' => 'Cuidado clínico y estético de la piel, cabello y uñas con tecnología de vanguardia.',
                'icon' => 'sparkles',
                'base_price' => 40.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Dermatoscopía de Lunares y Lesiones', 'price' => 35.00, 'duration_minutes' => 25, 'description' => 'Mapeo microscópico para prevención y detección temprana de cáncer de piel.'],
                    ['name' => 'Cauterización / Crioterapia de Lesiones', 'price' => 60.00, 'duration_minutes' => 30, 'description' => 'Remoción precisa de verrugas o lesiones cutáneas benignas.'],
                ],
            ],
            [
                'name' => 'Ginecología y Obstetricia',
                'slug' => 'ginecologia-y-obstetricia',
                'description' => 'Acompañamiento en salud femenina, control prenatal y salud reproductiva.',
                'icon' => 'shield-check',
                'base_price' => 45.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Papanicolaou y Colposcopía', 'price' => 40.00, 'duration_minutes' => 30, 'description' => 'Detección oportuna de alteraciones en cuello uterino.'],
                    ['name' => 'Ecografía Obstétrica 4D / Estructural', 'price' => 65.00, 'duration_minutes' => 35, 'description' => 'Evaluación avanzada del desarrollo y bienestar fetal.'],
                ],
            ],
            [
                'name' => 'Traumatología y Ortopedia',
                'slug' => 'traumatologia-y-ortopedia',
                'description' => 'Tratamiento de lesiones óseas, articulares, fracturas y lesiones deportivas.',
                'icon' => 'bone',
                'base_price' => 40.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Infiltración Articular Terapéutica', 'price' => 50.00, 'duration_minutes' => 25, 'description' => 'Aplicación localizada de analgésicos o antiinflamatorios articulares.'],
                    ['name' => 'Inmovilización / Yeso y Férula', 'price' => 45.00, 'duration_minutes' => 30, 'description' => 'Fijación profesional de esguinces o fracturas no desplazadas.'],
                ],
            ],
            [
                'name' => 'Medicina General',
                'slug' => 'medicina-general',
                'description' => 'Evaluación clínica inicial, chequeos de rutina y manejo de condiciones de salud comunes.',
                'icon' => 'stethoscope',
                'base_price' => 25.00,
                'default_duration_minutes' => 20,
                'procedures' => [
                    ['name' => 'Chequeo Clínico Preventivo', 'price' => 30.00, 'duration_minutes' => 30, 'description' => 'Revisión general de signos vitales, analítica y examen físico.'],
                    ['name' => 'Lavado de Oído / Extracción de Tapón', 'price' => 25.00, 'duration_minutes' => 20, 'description' => 'Higiene clínica segura del conducto auditivo.'],
                ],
            ],
            [
                'name' => 'Oftalmología',
                'slug' => 'oftalmologia',
                'description' => 'Salud visual integral, refracción computarizada y diagnóstico de patologías oculares.',
                'icon' => 'eye',
                'base_price' => 35.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Examen de Fondo de Ojo', 'price' => 25.00, 'duration_minutes' => 20, 'description' => 'Inspección de retina y nervio óptico.'],
                    ['name' => 'Tonometría (Medición de Presión Intraocular)', 'price' => 20.00, 'duration_minutes' => 15, 'description' => 'Descarte y control de glaucoma.'],
                ],
            ],
            [
                'name' => 'Odontología',
                'slug' => 'odontologia',
                'description' => 'Salud bucal preventiva, estética dental, endodoncia y periodoncia.',
                'icon' => 'smile',
                'base_price' => 30.00,
                'default_duration_minutes' => 45,
                'procedures' => [
                    ['name' => 'Profilaxis y Limpieza Dental con Ultrasonido', 'price' => 40.00, 'duration_minutes' => 45, 'description' => 'Eliminación profunda de placa bacteriana y sarro.'],
                    ['name' => 'Curación Dental con Resina Fotocurable', 'price' => 35.00, 'duration_minutes' => 40, 'description' => 'Restauración estética de piezas con caries dental.'],
                ],
            ],
        ];

        foreach ($specialties as $specData) {
            $procedures = $specData['procedures'];
            unset($specData['procedures']);

            $specialty = Specialty::firstOrCreate(
                ['slug' => $specData['slug']],
                $specData
            );

            foreach ($procedures as $procData) {
                Procedure::firstOrCreate(
                    [
                        'specialty_id' => $specialty->id,
                        'name' => $procData['name'],
                    ],
                    [
                        'price' => $procData['price'],
                        'duration_minutes' => $procData['duration_minutes'],
                        'description' => $procData['description'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
