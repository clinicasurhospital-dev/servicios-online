<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorAndScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $doctorsData = [
            [
                'name' => 'Dr. Carlos Soledispa Suárez',
                'email' => 'dr.carlos.soledispa@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-091823',
                'specialty_slug' => 'neurocirugia',
                'bio' => 'Especialista en Neurocirugía de alta complejidad con entrenamiento en biopsias estereotáxicas y neuromonitoreo intraoperatorio en Clínica Sur Hospital.',
                'room' => 'Torre Médica Sur - Consultorio 401',
                'price' => 60.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Jimmy Yaguana Torres',
                'email' => 'dr.jimmy.yaguana@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-084729',
                'specialty_slug' => 'cirugia-digestiva-y-general',
                'bio' => 'Cirujano Digestivo y Laparoscopista. Especialista en apendicectomías, cirugía biliar, hernias y paquetes quirúrgicos integrales.',
                'room' => 'Torre Quirúrgica - Consultorio 302',
                'price' => 50.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Omar Chungata Peláez',
                'email' => 'dr.omar.chungata@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-072615',
                'specialty_slug' => 'neurocirugia',
                'bio' => 'Neurocirujano especialista en abordaje de patologías vasculares, tumores cerebrales y cirugía de columna mínimamente invasiva.',
                'room' => 'Torre Médica Sur - Consultorio 402',
                'price' => 60.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Jorge Anoceto Díaz',
                'email' => 'dr.jorge.anoceto@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-093847',
                'specialty_slug' => 'urologia',
                'bio' => 'Urólogo especialista en salud prostática, endourología, litiasis renal y procedimientos urológicos de precisión.',
                'room' => 'Torre Médica Sur - Consultorio 204',
                'price' => 50.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Luis Machuca Galdós',
                'email' => 'dr.luis.machuca@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-081294',
                'specialty_slug' => 'nefrologia',
                'bio' => 'Nefrólogo clínico enfocado en la prevención y tratamiento de la insuficiencia renal crónica, glomerulopatías e hipertensión secundaria.',
                'room' => 'Torre Médica Sur - Consultorio 208',
                'price' => 50.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Miguel Unda Menéndez',
                'email' => 'dr.miguel.unda@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-074921',
                'specialty_slug' => 'gastroenterologia',
                'bio' => 'Gastroenterólogo y Endoscopista Digestivo. Especialista en diagnóstico de reflujo, gastritis, colonoscopías y biopsias digestivas.',
                'room' => 'Torre de Diagnóstico - Consultorio 105',
                'price' => 50.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dra. Maria Moreira Menendez',
                'email' => 'dra.maria.moreira@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-063819',
                'specialty_slug' => 'cuidados-criticos',
                'bio' => 'Especialista en Medicina Crítica y Terapia Intensiva. Coordinadora médica de soporte vital y monitorización continua.',
                'room' => 'Unidad de Terapia Intensiva (UTI)',
                'price' => 60.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dra. María Paola Illescas',
                'email' => 'dra.maria.illescas@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-092837',
                'specialty_slug' => 'otorrinolaringologia',
                'bio' => 'Otorrinolaringóloga especializada en diagnóstico de rinitis, sinusitis, hipoacusia, patología laríngea y procedimientos ORL.',
                'room' => 'Torre Médica Sur - Consultorio 301',
                'price' => 45.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dra. Daniela Guerrero Cagua',
                'email' => 'dra.daniela.guerrero@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-085710',
                'specialty_slug' => 'anestesiologia',
                'bio' => 'Médica Anestesióloga con amplia experiencia en anestesia para procedimientos quirúrgicos de alta complejidad y analgesia postoperatoria.',
                'room' => 'Área Quirúrgica SurHospital',
                'price' => 45.00,
                'duration' => 25,
            ],
            [
                'name' => 'Dr. Roberto Moncayo Reyna',
                'email' => 'dr.roberto.moncayo@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-071948',
                'specialty_slug' => 'gastroenterologia',
                'bio' => 'Gastroenterólogo tratante. Diagnóstico de patologías de hígado, vías biliares, páncreas y endoscopía diagnóstica y terapéutica.',
                'room' => 'Torre de Diagnóstico - Consultorio 106',
                'price' => 50.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Carlos León Borja',
                'email' => 'dr.carlos.leon@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-082910',
                'specialty_slug' => 'anestesiologia',
                'bio' => 'Especialista en Anestesiología y Reanimación. Valoración pre-anestésica de riesgo y control hemodinámico en quirófano.',
                'room' => 'Área Quirúrgica SurHospital',
                'price' => 45.00,
                'duration' => 25,
            ],
            [
                'name' => 'Dr. Manuel Infante Muñoz',
                'email' => 'dr.manuel.infante@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-091024',
                'specialty_slug' => 'anestesiologia',
                'bio' => 'Anestesiólogo clínico con enfoque en anestesia regional ecoguiada y protocolos de recuperación rápida post-quirúrgica.',
                'room' => 'Área Quirúrgica SurHospital',
                'price' => 45.00,
                'duration' => 25,
            ],
            [
                'name' => 'Dr. Juan Piguave Chalen',
                'email' => 'dr.juan.piguave@surhospital.net',
                'phone' => '042590670',
                'license' => 'MSP-EC-068392',
                'specialty_slug' => 'cirugia-digestiva-y-general',
                'bio' => 'Cirujano General con especial énfasis en cirugía abdominal de urgencia, patología herniaria y manejo postoperatorio.',
                'room' => 'Torre Quirúrgica - Consultorio 304',
                'price' => 50.00,
                'duration' => 30,
            ],
        ];

        foreach ($doctorsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
            $user->assignRole('DOCTOR');

            $specialty = Specialty::where('slug', $data['specialty_slug'])->first();

            $doctor = Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'license_number' => $data['license'],
                    'primary_specialty_name' => $specialty ? $specialty->name : 'Medicina Especializada',
                    'bio' => $data['bio'],
                    'room_office' => $data['room'],
                    'default_consultation_price' => $data['price'],
                    'default_slot_duration_minutes' => $data['duration'],
                    'is_active' => true,
                ]
            );

            if ($specialty) {
                $doctor->specialties()->syncWithoutDetaching([$specialty->id]);
            }

            // Create standard schedules Monday to Friday (and Saturday morning)
            for ($day = 1; $day <= 5; $day++) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'start_time' => '08:30:00',
                    ],
                    [
                        'end_time' => '13:00:00',
                        'is_active' => true,
                    ]
                );
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'start_time' => '14:30:00',
                    ],
                    [
                        'end_time' => '18:00:00',
                        'is_active' => true,
                    ]
                );
            }

            // Saturday Schedule
            DoctorSchedule::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => 6,
                    'start_time' => '09:00:00',
                ],
                [
                    'end_time' => '13:00:00',
                    'is_active' => true,
                ]
            );
        }
    }
}
