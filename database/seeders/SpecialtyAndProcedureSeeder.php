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
                'name' => 'Neurocirugía',
                'slug' => 'neurocirugia',
                'description' => 'Alta tecnología quirúrgica para patologías cerebrales y de columna, incluyendo biopsias guiadas por estereotaxia y neuromonitoreo.',
                'icon' => 'sparkles',
                'base_price' => 60.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Consulta Neuroquirúrgica Especializada', 'price' => 60.00, 'duration_minutes' => 30, 'description' => 'Evaluación neurológica integral de patologías craneales y raquídeas.'],
                    ['name' => 'Biopsia Cerebral por Estereotaxia (Valoración)', 'price' => 120.00, 'duration_minutes' => 45, 'description' => 'Protocolo de planificación para neuromonitoreo intraoperatorio en tumores parietales.'],
                    ['name' => 'Evaluación de Columna y Hernias Discales', 'price' => 70.00, 'duration_minutes' => 30, 'description' => 'Diagnóstico y abordaje quirúrgico mínimamente invasivo.'],
                ],
            ],
            [
                'name' => 'Cirugía Digestiva y General',
                'slug' => 'cirugia-digestiva-y-general',
                'description' => 'Cirugía laparoscópica y convencional con paquetes quirúrgicos integrales para apendicectomías, hernias y vesícula.',
                'icon' => 'shield-check',
                'base_price' => 50.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Paquete Quirúrgico: Apendicectomía', 'price' => 980.00, 'duration_minutes' => 60, 'description' => 'Incluye valoración prequirúrgica, 24h hospitalización, quirófano, honorarios, anestesia e insumos.'],
                    ['name' => 'Valoración Pre-quirúrgica Cirugía General', 'price' => 50.00, 'duration_minutes' => 30, 'description' => 'Evaluación clínica completa previa a intervención quirúrgica.'],
                    ['name' => 'Colecistectomía Laparoscópica (Valoración)', 'price' => 60.00, 'duration_minutes' => 30, 'description' => 'Planificación quirúrgica mínimamente invasiva de vesícula biliar.'],
                ],
            ],
            [
                'name' => 'Ginecología y Obstetricia',
                'slug' => 'ginecologia-y-obstetricia',
                'description' => 'Cuidado integral de la mujer, control prenatal de alto riesgo, parto humanizado y paquetes de cesárea.',
                'icon' => 'baby',
                'base_price' => 45.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Paquete Quirúrgico: Cesárea Integral', 'price' => 850.00, 'duration_minutes' => 60, 'description' => 'Incluye valoración prequirúrgica, 24h hospitalización, quirófano, honorarios, anestesia e insumos.'],
                    ['name' => 'Control Prenatal y Monitoreo Fetal', 'price' => 45.00, 'duration_minutes' => 30, 'description' => 'Evaluación de salud materno-fetal periódica.'],
                    ['name' => 'Papanicolaou y Colposcopía Digital', 'price' => 50.00, 'duration_minutes' => 30, 'description' => 'Detección temprana y diagnóstico de lesiones cervicales.'],
                ],
            ],
            [
                'name' => 'Gastroenterología',
                'slug' => 'gastroenterologia',
                'description' => 'Diagnóstico y tratamiento de patologías digestivas, endoscopía digestiva alta y colonoscopías con sedación asistida.',
                'icon' => 'stethoscope',
                'base_price' => 50.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Consulta Gastroenterológica', 'price' => 50.00, 'duration_minutes' => 30, 'description' => 'Diagnóstico de gastritis, reflujo, colon irritable y patología hepatobiliar.'],
                    ['name' => 'Endoscopía Digestiva Alta con Sedación', 'price' => 140.00, 'duration_minutes' => 40, 'description' => 'Visualización directa de esófago, estómago y duodeno.'],
                    ['name' => 'Colonoscopía Diagnóstica Total', 'price' => 180.00, 'duration_minutes' => 45, 'description' => 'Inspección de colon y recto con toma de biopsias.'],
                ],
            ],
            [
                'name' => 'Urología',
                'slug' => 'urologia',
                'description' => 'Atención especializada en el sistema urinario masculino/femenino y aparato reproductor masculino.',
                'icon' => 'shield-check',
                'base_price' => 50.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Consulta Urológica Especializada', 'price' => 50.00, 'duration_minutes' => 30, 'description' => 'Diagnóstico y control prostático, litiasis urinaria y vías urinarias.'],
                    ['name' => 'Ecografía Urológica y Prostática', 'price' => 45.00, 'duration_minutes' => 25, 'description' => 'Evaluación ecográfica de riñones, vejiga y próstata.'],
                    ['name' => 'Cistoscopía Diagnóstica', 'price' => 120.00, 'duration_minutes' => 35, 'description' => 'Exploración endoscópica de uretra y vejiga.'],
                ],
            ],
            [
                'name' => 'Nefrología',
                'slug' => 'nefrologia',
                'description' => 'Prevención, diagnóstico y tratamiento de enfermedades del riñón, hipertensión arterial y terapia renal.',
                'icon' => 'heart',
                'base_price' => 50.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Consulta Nefrológica Integral', 'price' => 50.00, 'duration_minutes' => 30, 'description' => 'Evaluación de filtrado glomerular, proteinuria y función renal.'],
                    ['name' => 'Manejo de Hipertensión de Difícil Control', 'price' => 55.00, 'duration_minutes' => 30, 'description' => 'Abordaje especializado nefro-protector.'],
                ],
            ],
            [
                'name' => 'Otorrinolaringología',
                'slug' => 'otorrinolaringologia',
                'description' => 'Diagnóstico y tratamiento médico-quirúrgico de enfermedades del oído, nariz, senos paranasales y garganta.',
                'icon' => 'eye',
                'base_price' => 45.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Consulta Otorrinolaringológica', 'price' => 45.00, 'duration_minutes' => 30, 'description' => 'Revisión con otoscopía y rinoscopía de vías aéreas superiores.'],
                    ['name' => 'Lavado de Oído / Limpieza Instrumental', 'price' => 30.00, 'duration_minutes' => 20, 'description' => 'Extracción segura de cerumen o cuerpos extraños.'],
                    ['name' => 'Cauterización de Vasos Nasales', 'price' => 60.00, 'duration_minutes' => 25, 'description' => 'Tratamiento de epistaxis recurrente.'],
                ],
            ],
            [
                'name' => 'Cardiología y Hemodinamia',
                'slug' => 'cardiologia-y-hemodinamia',
                'description' => 'Sala de hemodinamia con tecnología de fluoroscopía para cateterismo, coronariografía y diagnóstico cardiovascular.',
                'icon' => 'heart',
                'base_price' => 50.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Consulta Cardiológica con ECG', 'price' => 55.00, 'duration_minutes' => 30, 'description' => 'Electrocardiograma basal y evaluación cardiovascular.'],
                    ['name' => 'Ecocardiograma Doppler Color', 'price' => 80.00, 'duration_minutes' => 40, 'description' => 'Estudio de función valvular y contractilidad miocárdica.'],
                    ['name' => 'Estudio Hemodinámico / Coronariografía (Planificación)', 'price' => 150.00, 'duration_minutes' => 45, 'description' => 'Protocolo en sala de hemodinamia para cateterismo y angiografía.'],
                ],
            ],
            [
                'name' => 'Diagnóstico por Imágenes (Radiología)',
                'slug' => 'diagnostico-por-imagenes',
                'description' => 'Tomografía multicorte (TAC), Resonancia Magnética (RM), Rayos X digitales y Ecografías de alta resolución 24/7.',
                'icon' => 'sparkles',
                'base_price' => 40.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Tomografía Computarizada (TAC) Simple/Contrastada', 'price' => 110.00, 'duration_minutes' => 30, 'description' => 'Cerebro, cuello, tórax, abdomen y pelvis.'],
                    ['name' => 'Resonancia Magnética (RM) de Alta Definición', 'price' => 170.00, 'duration_minutes' => 45, 'description' => 'Cráneo, columna vertebral, articulaciones y mamas.'],
                    ['name' => 'Rayos X Digital Multirregión', 'price' => 35.00, 'duration_minutes' => 20, 'description' => 'Tórax, columna, abdomen o huesos largos.'],
                    ['name' => 'Ecografía Vascular Doppler', 'price' => 60.00, 'duration_minutes' => 30, 'description' => 'Evaluación de flujo arterial y venoso periférico.'],
                ],
            ],
            [
                'name' => 'Cuidados Críticos y Terapia Intensiva',
                'slug' => 'cuidados-criticos',
                'description' => 'Manejo médico especializado de pacientes en estado crítico y monitoreo permanente.',
                'icon' => 'shield-check',
                'base_price' => 60.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Interconsulta Médica en Terapia Intensiva', 'price' => 70.00, 'duration_minutes' => 30, 'description' => 'Evaluación hemodinámica y soporte vital avanzado.'],
                ],
            ],
            [
                'name' => 'Anestesiología',
                'slug' => 'anestesiologia',
                'description' => 'Valoración preanestésica, control de analgesia perioperatoria y seguridad en todos los procedimientos.',
                'icon' => 'shield-check',
                'base_price' => 45.00,
                'default_duration_minutes' => 25,
                'procedures' => [
                    ['name' => 'Valoración Pre-Anestésica Oficial', 'price' => 45.00, 'duration_minutes' => 25, 'description' => 'Evaluación de riesgo quirúrgico ASA y planificación anestésica.'],
                ],
            ],
            [
                'name' => 'Pediatría',
                'slug' => 'pediatria',
                'description' => 'Atención integral desde recién nacidos hasta adolescentes con calidez humana y enfoque preventivo.',
                'icon' => 'baby',
                'base_price' => 35.00,
                'default_duration_minutes' => 30,
                'procedures' => [
                    ['name' => 'Control de Crecimiento y Niño Sano', 'price' => 35.00, 'duration_minutes' => 30, 'description' => 'Evaluación periódica de desarrollo psicomotor y nutricional.'],
                    ['name' => 'Esquema de Vacunación Pediátrica', 'price' => 30.00, 'duration_minutes' => 20, 'description' => 'Administración segura de biológicos.'],
                ],
            ],
            [
                'name' => 'Medicina General',
                'slug' => 'medicina-general',
                'description' => 'Consulta médica general, chequeos preventivos y orientación diagnóstica oportuna.',
                'icon' => 'stethoscope',
                'base_price' => 25.00,
                'default_duration_minutes' => 20,
                'procedures' => [
                    ['name' => 'Consulta Médica General Preventiva', 'price' => 25.00, 'duration_minutes' => 20, 'description' => 'Evaluación clínica de rutina, examen físico y prescripción.'],
                ],
            ],
        ];

        foreach ($specialties as $specData) {
            $procedures = $specData['procedures'] ?? [];
            unset($specData['procedures']);

            $specialty = Specialty::updateOrCreate(
                ['slug' => $specData['slug']],
                $specData + ['is_active' => true]
            );

            foreach ($procedures as $procData) {
                Procedure::updateOrCreate(
                    [
                        'specialty_id' => $specialty->id,
                        'name' => $procData['name'],
                    ],
                    $procData + ['is_active' => true]
                );
            }
        }
    }
}
