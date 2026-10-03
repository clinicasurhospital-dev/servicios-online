<?php

namespace Database\Seeders;

use App\Domain\Clinical\Enums\FieldType;
use App\Domain\Clinical\Enums\SectionVisibility;
use App\Domain\Clinical\Enums\TemplateStatus;
use App\Domain\Clinical\Services\ClinicalTemplateService;
use App\Models\ClinicalTemplate;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClinicalModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Register Clinical Permissions
        $permissions = [
            'clinical_record_view',
            'clinical_record_edit',
            'clinical_encounter_create',
            'clinical_encounter_edit',
            'clinical_encounter_finalize',
            'clinical_addendum_create',
            'clinical_template_view',
            'clinical_template_create',
            'clinical_template_edit',
            'clinical_template_publish',
            'clinical_document_upload',
            'clinical_document_view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2. Assign to Roles
        $admin = Role::findByName('ADMIN', 'web');
        $superAdmin = Role::findByName('SUPER_ADMIN', 'web');
        $doctor = Role::findByName('DOCTOR', 'web');
        $reception = Role::findByName('RECEPTION', 'web');
        $patient = Role::findByName('PATIENT', 'web');

        $adminPermissions = [
            'clinical_record_view', 'clinical_record_edit',
            'clinical_template_view', 'clinical_template_create', 'clinical_template_edit', 'clinical_template_publish',
            'clinical_document_upload', 'clinical_document_view',
        ];
        $admin->givePermissionTo($adminPermissions);

        $doctorPermissions = [
            'clinical_record_view', 'clinical_record_edit',
            'clinical_encounter_create', 'clinical_encounter_edit', 'clinical_encounter_finalize',
            'clinical_addendum_create', 'clinical_template_view',
            'clinical_document_upload', 'clinical_document_view',
        ];
        $doctor->givePermissionTo($doctorPermissions);

        // Reception only views non-sensitive clinical record summaries and appointment details
        $reception->givePermissionTo(['clinical_record_view']);

        // Patient can view own visible records and upload consent/reports
        $patient->givePermissionTo(['clinical_document_upload', 'clinical_document_view']);

        // 3. Seed Default Specialties if not present
        $medGeneral = Specialty::firstOrCreate(['slug' => 'medicina-general'], ['name' => 'Medicina General', 'base_price' => 35.00, 'default_duration_minutes' => 30, 'is_active' => true]);
        $cardiologia = Specialty::firstOrCreate(['slug' => 'cardiologia'], ['name' => 'Cardiología', 'base_price' => 50.00, 'default_duration_minutes' => 30, 'is_active' => true]);
        $pediatria = Specialty::firstOrCreate(['slug' => 'pediatria'], ['name' => 'Pediatría', 'base_price' => 45.00, 'default_duration_minutes' => 30, 'is_active' => true]);
        $ginecologia = Specialty::firstOrCreate(['slug' => 'ginecologia'], ['name' => 'Ginecología', 'base_price' => 45.00, 'default_duration_minutes' => 30, 'is_active' => true]);

        $adminUser = User::role(['SUPER_ADMIN', 'ADMIN'])->first() ?? User::first();
        if (!$adminUser) {
            $adminUser = User::firstOrCreate(
                ['email' => 'admin.clinical@surhospital.com'],
                [
                    'name' => 'Admin Clínico',
                    'phone' => '+593 99 999 9999',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
            $adminUser->assignRole('ADMIN');
        }
        $adminId = $adminUser->id;

        $templateService = app(ClinicalTemplateService::class);

        // 4. Seed Template: Medicina General
        if (!ClinicalTemplate::where('slug', 'plantilla-medicina-general')->exists()) {
            $tMed = $templateService->createTemplate([
                'specialty_id' => $medGeneral->id,
                'name' => 'Plantilla Medicina General',
                'description' => 'Historia clínica estándar para consulta externa de medicina general y familiar.',
                'is_active' => true,
            ], $adminId);

            $vMed = $tMed->latestVersion;
            $this->seedMedicinaGeneralStructure($vMed);
            $templateService->publishVersion($vMed, $adminId);
        }

        // 5. Seed Template: Cardiología
        if (!ClinicalTemplate::where('slug', 'plantilla-cardiologia')->exists()) {
            $tCardio = $templateService->createTemplate([
                'specialty_id' => $cardiologia->id,
                'name' => 'Plantilla Cardiología',
                'description' => 'Historia clínica especializada para evaluación cardiovascular y electrocardiográfica.',
                'is_active' => true,
            ], $adminId);

            $vCardio = $tCardio->latestVersion;
            $this->seedCardiologiaStructure($vCardio);
            $templateService->publishVersion($vCardio, $adminId);
        }

        // 6. Seed Template: Pediatría
        if (!ClinicalTemplate::where('slug', 'plantilla-pediatria')->exists()) {
            $tPed = $templateService->createTemplate([
                'specialty_id' => $pediatria->id,
                'name' => 'Plantilla Pediatría',
                'description' => 'Historia clínica pediátrica con enfoque en desarrollo psicomotor y percentiles de crecimiento.',
                'is_active' => true,
            ], $adminId);

            $vPed = $tPed->latestVersion;
            $this->seedPediatriaStructure($vPed);
            $templateService->publishVersion($vPed, $adminId);
        }

        // 7. Seed Template: Ginecología
        if (!ClinicalTemplate::where('slug', 'plantilla-ginecologia')->exists()) {
            $tGin = $templateService->createTemplate([
                'specialty_id' => $ginecologia->id,
                'name' => 'Plantilla Ginecología',
                'description' => 'Historia clínica gineco-obstétrica integral.',
                'is_active' => true,
            ], $adminId);

            $vGin = $tGin->latestVersion;
            $this->seedGinecologiaStructure($vGin);
            $templateService->publishVersion($vGin, $adminId);
        }
    }

    protected function seedMedicinaGeneralStructure($version): void
    {
        // Section 1: Motivo de Consulta y Enfermedad Actual
        $s1 = $version->sections()->create([
            'name' => 'Motivo de Consulta y Enfermedad Actual',
            'description' => 'Descripción del cuadro clínico que motiva la consulta.',
            'order' => 1,
            'is_required' => true,
        ]);
        $s1->fields()->create([
            'name' => 'Tipo de Consulta',
            'code' => 'consultation_type_custom',
            'label' => 'Tipo de Consulta',
            'field_type' => FieldType::SELECT->value,
            'order' => 1,
            'is_required' => true,
            'is_patient_visible' => true,
        ])->options()->createMany([
            ['label' => 'Primera Consulta', 'value' => 'INITIAL', 'order' => 1, 'is_default' => true],
            ['label' => 'Control / Seguimiento', 'value' => 'FOLLOW_UP', 'order' => 2],
            ['label' => 'Revisión de Exámenes', 'value' => 'EXAM_REVIEW', 'order' => 3],
            ['label' => 'Urgencia Asistencial', 'value' => 'EMERGENCY', 'order' => 4],
        ]);
        $s1->fields()->create([
            'name' => 'Motivo de Consulta',
            'code' => 'chief_complaint',
            'label' => 'Motivo Principal de Consulta',
            'field_type' => FieldType::TEXTAREA->value,
            'placeholder' => 'Describa brevemente el síntoma o causa principal referida por el paciente...',
            'order' => 2,
            'is_required' => true,
            'is_patient_visible' => true,
        ]);
        $s1->fields()->create([
            'name' => 'Enfermedad Actual',
            'code' => 'present_illness',
            'label' => 'Evolución de la Enfermedad Actual (Cronología, características)',
            'field_type' => FieldType::TEXTAREA->value,
            'order' => 3,
            'is_required' => true,
            'is_patient_visible' => true,
        ]);

        // Section 2: Signos Vitales y Antropometría
        $s2 = $version->sections()->create([
            'name' => 'Signos Vitales y Antropometría',
            'description' => 'Mediciones biométricas basales.',
            'order' => 2,
            'is_required' => false,
        ]);
        $s2->fields()->createMany([
            ['name' => 'Peso', 'code' => 'weight_kg', 'label' => 'Peso (kg)', 'field_type' => FieldType::DECIMAL->value, 'unit' => 'kg', 'validation_rules' => ['min' => 1, 'max' => 400], 'order' => 1, 'is_patient_visible' => true],
            ['name' => 'Talla', 'code' => 'height_cm', 'label' => 'Talla (cm)', 'field_type' => FieldType::DECIMAL->value, 'unit' => 'cm', 'validation_rules' => ['min' => 30, 'max' => 250], 'order' => 2, 'is_patient_visible' => true],
            ['name' => 'Presión Arterial Sistólica', 'code' => 'bp_systolic', 'label' => 'PA Sistólica', 'field_type' => FieldType::INTEGER->value, 'unit' => 'mmHg', 'validation_rules' => ['min' => 50, 'max' => 260], 'order' => 3, 'is_clinical_alert' => true, 'is_patient_visible' => true],
            ['name' => 'Presión Arterial Diastólica', 'code' => 'bp_diastolic', 'label' => 'PA Diastólica', 'field_type' => FieldType::INTEGER->value, 'unit' => 'mmHg', 'validation_rules' => ['min' => 30, 'max' => 160], 'order' => 4, 'is_clinical_alert' => true, 'is_patient_visible' => true],
            ['name' => 'Frecuencia Cardíaca', 'code' => 'heart_rate', 'label' => 'Frecuencia Cardíaca (LPM)', 'field_type' => FieldType::INTEGER->value, 'unit' => 'bpm', 'validation_rules' => ['min' => 30, 'max' => 240], 'order' => 5, 'is_patient_visible' => true],
            ['name' => 'Temperatura', 'code' => 'temperature_c', 'label' => 'Temperatura Corporal (°C)', 'field_type' => FieldType::DECIMAL->value, 'unit' => '°C', 'validation_rules' => ['min' => 33, 'max' => 43], 'order' => 6, 'is_patient_visible' => true],
            ['name' => 'Saturación de Oxígeno', 'code' => 'oxygen_sat', 'label' => 'Saturación O2 (%)', 'field_type' => FieldType::INTEGER->value, 'unit' => '%', 'validation_rules' => ['min' => 50, 'max' => 100], 'order' => 7, 'is_patient_visible' => true],
        ]);

        // Section 3: Examen Físico
        $s3 = $version->sections()->create([
            'name' => 'Examen Físico por Sistemas',
            'description' => 'Hallazgos a la inspección, palpación, auscultación y percusión.',
            'order' => 3,
        ]);
        $s3->fields()->createMany([
            ['name' => 'Estado General', 'code' => 'general_status', 'label' => 'Estado General y Nivel de Consciencia', 'field_type' => FieldType::TEXTAREA->value, 'order' => 1, 'is_patient_visible' => true],
            ['name' => 'Cabeza y Cuello', 'code' => 'head_neck', 'label' => 'Cabeza y Cuello', 'field_type' => FieldType::TEXT->value, 'order' => 2, 'is_patient_visible' => false],
            ['name' => 'Tórax y Pulmones', 'code' => 'chest_lungs', 'label' => 'Tórax y Pulmones', 'field_type' => FieldType::TEXT->value, 'order' => 3, 'is_patient_visible' => false],
            ['name' => 'Abdomen', 'code' => 'abdomen', 'label' => 'Abdomen', 'field_type' => FieldType::TEXT->value, 'order' => 4, 'is_patient_visible' => false],
            ['name' => 'Extremidades', 'code' => 'extremities', 'label' => 'Extremidades y Neurológico', 'field_type' => FieldType::TEXT->value, 'order' => 5, 'is_patient_visible' => false],
        ]);

        // Section 4: Plan, Tratamiento e Indicaciones
        $s4 = $version->sections()->create([
            'name' => 'Plan Terapéutico e Indicaciones',
            'description' => 'Receta médica, recomendaciones y cita de control.',
            'order' => 4,
            'is_required' => true,
        ]);
        $s4->fields()->createMany([
            ['name' => 'Plan Farmacológico', 'code' => 'pharmacological_plan', 'label' => 'Tratamiento Farmacológico (Prescripción)', 'field_type' => FieldType::TEXTAREA->value, 'order' => 1, 'is_required' => true, 'is_patient_visible' => true],
            ['name' => 'Indicaciones No Farmacológicas', 'code' => 'non_pharm_instructions', 'label' => 'Recomendaciones Higiénico-Dietéticas y Cuidados', 'field_type' => FieldType::TEXTAREA->value, 'order' => 2, 'is_patient_visible' => true],
            ['name' => 'Signos de Alarma', 'code' => 'warning_signs', 'label' => 'Signos de Alarma / Cuándo acudir a Urgencias', 'field_type' => FieldType::TEXTAREA->value, 'order' => 3, 'is_patient_visible' => true],
            ['name' => 'Próximo Control', 'code' => 'next_follow_up', 'label' => 'Próxima Cita / Control sugerido', 'field_type' => FieldType::TEXT->value, 'order' => 4, 'is_patient_visible' => true],
        ]);
    }

    protected function seedCardiologiaStructure($version): void
    {
        $s1 = $version->sections()->create([
            'name' => 'Evaluación Cardiovascular',
            'description' => 'Motivo, factores de riesgo y síntomas cardinales cardiológicos.',
            'order' => 1,
            'is_required' => true,
        ]);
        $s1->fields()->create([
            'name' => 'Dolor Torácico',
            'code' => 'chest_pain_evaluation',
            'label' => '¿Presenta dolor torácico / opresión precordial?',
            'field_type' => FieldType::RADIO->value,
            'order' => 1,
            'is_clinical_alert' => true,
        ])->options()->createMany([
            ['label' => 'No presenta dolor torácico', 'value' => 'NO', 'order' => 1, 'is_default' => true],
            ['label' => 'Dolor típico anginoso (Opresivo, irradiado a mandíbula/brazo)', 'value' => 'TYPICAL_ANGINA', 'order' => 2],
            ['label' => 'Dolor atípico / osteomuscular', 'value' => 'ATYPICAL', 'order' => 3],
        ]);

        $s1->fields()->create([
            'name' => 'Factores de Riesgo',
            'code' => 'cardio_risk_factors',
            'label' => 'Factores de Riesgo Cardiovascular Presentes',
            'field_type' => FieldType::MULTISELECT->value,
            'order' => 2,
            'is_clinical_alert' => true,
        ])->options()->createMany([
            ['label' => 'Hipertensión Arterial', 'value' => 'HTA', 'order' => 1],
            ['label' => 'Diabetes Mellitus', 'value' => 'DM', 'order' => 2],
            ['label' => 'Dislipidemia / Colesterol elevado', 'value' => 'DISLIP', 'order' => 3],
            ['label' => 'Tabaquismo Activo', 'value' => 'SMOKER', 'order' => 4],
            ['label' => 'Sedentarismo / Obesidad', 'value' => 'OBESITY', 'order' => 5],
            ['label' => 'Antecedente Familiar de Infarto Temprano', 'value' => 'FAMILY_CVD', 'order' => 6],
        ]);

        $s2 = $version->sections()->create([
            'name' => 'Hemodinamia y Electrocardiografía',
            'description' => 'Presión arterial estricta, auscultación cardíaca y ECG.',
            'order' => 2,
        ]);
        $s2->fields()->createMany([
            ['name' => 'PA Brazo Derecho', 'code' => 'bp_right_arm', 'label' => 'PA Brazo Derecho (Sistólica/Diastólica)', 'field_type' => FieldType::TEXT->value, 'placeholder' => '120/80', 'order' => 1, 'is_clinical_alert' => true],
            ['name' => 'Auscultación Cardíaca', 'code' => 'cardiac_auscultation', 'label' => 'Ruidos Cardíacos / Soplos', 'field_type' => FieldType::TEXTAREA->value, 'placeholder' => 'R1 y R2 rítmicos, normofonéticos, sin soplos ni extratonos...', 'order' => 2],
            ['name' => 'Informe Electrocardiograma (ECG)', 'code' => 'ecg_report', 'label' => 'Interpretación del Electrocardiograma (Ritmo, Eje, PR, QRS, QT)', 'field_type' => FieldType::TEXTAREA->value, 'order' => 3],
        ]);

        $s3 = $version->sections()->create([
            'name' => 'Plan Cardiológico y Metas',
            'description' => 'Ajuste farmacológico, metas tensionales y solicitud de estudios.',
            'order' => 3,
            'is_required' => true,
        ]);
        $s3->fields()->createMany([
            ['name' => 'Plan Terapéutico', 'code' => 'cardio_therapy_plan', 'label' => 'Tratamiento Farmacológico Cardiológico', 'field_type' => FieldType::TEXTAREA->value, 'order' => 1, 'is_required' => true],
            ['name' => 'Estudios Complementarios', 'code' => 'cardio_lab_orders', 'label' => 'Estudios Solicitados (Ecocardiograma, Holter, MAPA, Prueba de Esfuerzo)', 'field_type' => FieldType::TEXTAREA->value, 'order' => 2],
        ]);
    }

    protected function seedPediatriaStructure($version): void
    {
        $s1 = $version->sections()->create([
            'name' => 'Antecedentes Perinatales y Desarrollo',
            'description' => 'Edad gestacional, parto, peso al nacer y vacunas.',
            'order' => 1,
        ]);
        $s1->fields()->createMany([
            ['name' => 'Edad Gestacional', 'code' => 'gestational_weeks', 'label' => 'Semanas de Gestación al Nacer', 'field_type' => FieldType::INTEGER->value, 'unit' => 'sem', 'order' => 1],
            ['name' => 'Peso al Nacer', 'code' => 'birth_weight_g', 'label' => 'Peso al Nacer (gramos)', 'field_type' => FieldType::INTEGER->value, 'unit' => 'g', 'order' => 2],
            ['name' => 'Tipo de Parto', 'code' => 'delivery_type', 'label' => 'Tipo de Parto (Eutócico / Cesárea)', 'field_type' => FieldType::TEXT->value, 'order' => 3],
            ['name' => 'Esquema de Vacunación', 'code' => 'vaccination_status', 'label' => 'Esquema de Vacunación Completo para la Edad', 'field_type' => FieldType::BOOLEAN->value, 'order' => 4],
            ['name' => 'Alimentación / Lactancia', 'code' => 'nutrition_type', 'label' => 'Tipo de Lactancia / Alimentación Complementaria', 'field_type' => FieldType::TEXT->value, 'order' => 5],
        ]);

        $s2 = $version->sections()->create([
            'name' => 'Somatometría y Percentiles',
            'description' => 'Peso, Talla y Perímetro Cefálico.',
            'order' => 2,
        ]);
        $s2->fields()->createMany([
            ['name' => 'Peso Actual', 'code' => 'ped_weight', 'label' => 'Peso Actual (kg)', 'field_type' => FieldType::DECIMAL->value, 'unit' => 'kg', 'order' => 1],
            ['name' => 'Talla / Longitud', 'code' => 'ped_height', 'label' => 'Talla (cm)', 'field_type' => FieldType::DECIMAL->value, 'unit' => 'cm', 'order' => 2],
            ['name' => 'Perímetro Cefálico', 'code' => 'head_circumference', 'label' => 'Perímetro Cefálico (cm)', 'field_type' => FieldType::DECIMAL->value, 'unit' => 'cm', 'order' => 3],
            ['name' => 'Evaluación de Percentiles', 'code' => 'percentile_assessment', 'label' => 'Curva de Crecimiento OMS / Percentiles', 'field_type' => FieldType::TEXT->value, 'order' => 4],
        ]);

        $s3 = $version->sections()->create([
            'name' => 'Indicaciones y Pautas de Crianza',
            'description' => 'Dosis ponderal, indicaciones a los padres y signos de alerta.',
            'order' => 3,
            'is_required' => true,
        ]);
        $s3->fields()->createMany([
            ['name' => 'Tratamiento Pediátrico', 'code' => 'ped_treatment', 'label' => 'Tratamiento Farmacológico (Dosis calculada por kg de peso)', 'field_type' => FieldType::TEXTAREA->value, 'order' => 1, 'is_required' => true],
            ['name' => 'Pautas para Padres', 'code' => 'parent_guidelines', 'label' => 'Recomendaciones de Cuidado y Alarma', 'field_type' => FieldType::TEXTAREA->value, 'order' => 2],
        ]);
    }

    protected function seedGinecologiaStructure($version): void
    {
        $s1 = $version->sections()->create([
            'name' => 'Antecedentes Gineco-Obstétricos (AGO)',
            'description' => 'Menarquia, FUM, fórmula obstétrica y anticoncepción.',
            'order' => 1,
            'is_required' => true,
        ]);
        $s1->fields()->createMany([
            ['name' => 'Fecha Última Menstruación (FUM)', 'code' => 'fum_date', 'label' => 'Fecha de Última Menstruación (FUM)', 'field_type' => FieldType::DATE->value, 'order' => 1, 'is_required' => true],
            ['name' => 'Menarquia', 'code' => 'menarche_age', 'label' => 'Edad de Menarquia (años)', 'field_type' => FieldType::INTEGER->value, 'unit' => 'años', 'order' => 2],
            ['name' => 'Fórmula Obstétrica', 'code' => 'obstetric_formula', 'label' => 'Fórmula (G: Gestas, P: Partos, C: Cesáreas, A: Abortos)', 'field_type' => FieldType::TEXT->value, 'placeholder' => 'G: 2, P: 1, C: 1, A: 0', 'order' => 3],
            ['name' => 'Método Anticonceptivo', 'code' => 'contraceptive_method', 'label' => 'Método Anticonceptivo Actual', 'field_type' => FieldType::TEXT->value, 'order' => 4],
            ['name' => 'Último Papanicolaou (PAP)', 'code' => 'last_pap_date', 'label' => 'Fecha y Resultado de Último PAP', 'field_type' => FieldType::TEXT->value, 'order' => 5],
        ]);

        $s2 = $version->sections()->create([
            'name' => 'Examen Físico Ginecológico',
            'description' => 'Examen mamario, espéculo, tacto bimanual.',
            'order' => 2,
        ]);
        $s2->fields()->createMany([
            ['name' => 'Examen de Mamas', 'code' => 'breast_exam', 'label' => 'Examen Físico de Mamas', 'field_type' => FieldType::TEXTAREA->value, 'order' => 1],
            ['name' => 'Especuloscopía y Tacto Vaginal', 'code' => 'pelvic_exam', 'label' => 'Especuloscopía y Tacto Bimanual', 'field_type' => FieldType::TEXTAREA->value, 'order' => 2],
        ]);

        $s3 = $version->sections()->create([
            'name' => 'Diagnóstico y Plan Gineco-Obstétrico',
            'description' => 'Diagnóstico, tratamiento y citación.',
            'order' => 3,
            'is_required' => true,
        ]);
        $s3->fields()->createMany([
            ['name' => 'Plan Terapéutico', 'code' => 'gin_treatment_plan', 'label' => 'Tratamiento e Indicaciones Ginecológicas', 'field_type' => FieldType::TEXTAREA->value, 'order' => 1, 'is_required' => true],
        ]);
    }
}
