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
                'name' => 'Dr. Carlos Mendoza',
                'email' => 'dr.carlos.mendoza@surhospital.com',
                'phone' => '+593 99 876 5432',
                'license' => 'MSP-09-8472',
                'specialty_slug' => 'cardiologia',
                'bio' => 'Cardiólogo clínico egresado de la Universidad de Guayaquil con subespecialidad en ecocardiografía avanzada. Más de 12 años de experiencia.',
                'room' => 'Torre Médica A - Cons. 302',
                'price' => 45.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dra. Elena Rodríguez',
                'email' => 'dra.elena.rodriguez@surhospital.com',
                'phone' => '+593 98 765 4321',
                'license' => 'MSP-09-9182',
                'specialty_slug' => 'pediatria',
                'bio' => 'Especialista en Pediatría y Neonatología. Dedicada a la atención con enfoque humanizado y desarrollo integral en la primera infancia.',
                'room' => 'Torre Médica B - Cons. 104',
                'price' => 35.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Roberto Silva',
                'email' => 'dr.roberto.silva@surhospital.com',
                'phone' => '+593 97 654 3210',
                'license' => 'MSP-09-7261',
                'specialty_slug' => 'traumatologia-y-ortopedia',
                'bio' => 'Cirujano Traumatólogo con fellowship en artroscopía de rodilla y hombro. Médico de atletas y traumatología deportiva.',
                'room' => 'Torre Médica A - Cons. 208',
                'price' => 40.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dra. Valeria Torres',
                'email' => 'dra.valeria.torres@surhospital.com',
                'phone' => '+593 96 543 2109',
                'license' => 'MSP-09-6534',
                'specialty_slug' => 'ginecologia-y-obstetricia',
                'bio' => 'Ginecóloga obstetra con especialización en diagnóstico prenatal, colposcopía y salud integral de la mujer.',
                'room' => 'Torre Médica B - Cons. 201',
                'price' => 45.00,
                'duration' => 30,
            ],
            [
                'name' => 'Dr. Andrés Pérez',
                'email' => 'dr.andres.perez@surhospital.com',
                'phone' => '+593 95 432 1098',
                'license' => 'MSP-09-5421',
                'specialty_slug' => 'dermatologia',
                'bio' => 'Dermatólogo clínico y quirúrgico. Especialista en diagnóstico de acné severo, melasma, psoriasis y procedimientos dermatológicos.',
                'room' => 'Torre Médica A - Cons. 105',
                'price' => 40.00,
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
                    'primary_specialty_name' => $specialty ? $specialty->name : 'Medicina',
                    'bio' => $data['bio'],
                    'room_office' => $data['room'],
                    'default_consultation_price' => $data['price'],
                    'default_slot_duration_minutes' => $data['duration'],
                    'is_active' => true,
                ]
            );

            if ($specialty) {
                $doctor->specialties()->syncWithoutDetaching([$specialty->id]);
                // Attach specialty procedures
                foreach ($specialty->procedures as $proc) {
                    $doctor->procedures()->syncWithoutDetaching([$proc->id]);
                }
            }

            // Create Standard Working Schedules: Monday to Friday 08:00-13:00 and 14:00-18:00, Saturday 08:00-13:00
            // Days 1 (Mon) to 5 (Fri)
            for ($day = 1; $day <= 5; $day++) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'start_time' => '08:00:00',
                    ],
                    [
                        'end_time' => '13:00:00',
                        'slot_duration_minutes' => $doctor->default_slot_duration_minutes,
                        'is_active' => true,
                    ]
                );

                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'start_time' => '14:00:00',
                    ],
                    [
                        'end_time' => '18:00:00',
                        'slot_duration_minutes' => $doctor->default_slot_duration_minutes,
                        'is_active' => true,
                    ]
                );
            }

            // Saturday (Day 6)
            DoctorSchedule::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => 6,
                    'start_time' => '08:00:00',
                ],
                [
                    'end_time' => '13:00:00',
                    'slot_duration_minutes' => $doctor->default_slot_duration_minutes,
                    'is_active' => true,
                ]
            );
        }
    }
}
