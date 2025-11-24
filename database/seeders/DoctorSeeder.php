<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        // Doctor 1: Dr. Carlos Mendoza
        $doctor1 = Doctor::create([
            'name' => 'Dr. Carlos Mendoza',
            'slug' => 'dr-carlos-mendoza',
            'specialty' => 'Cirugía General',
            'bio' => 'Cirujano general con 15 años de experiencia en cirugía laparoscópica y procedimientos mínimamente invasivos.',
            'email' => 'carlos.mendoza@hospital.com',
            'phone' => '+57 300 1234567',
            'is_active' => true,
        ]);

        // Horario: Lunes a Viernes 8:00 - 12:00 y 14:00 - 18:00
        $weekdays = [1, 2, 3, 4, 5]; // Lunes a Viernes
        foreach ($weekdays as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor1->id,
                'day_of_week' => $day,
                'start_time' => '08:00',
                'end_time' => '12:00',
            ]);
            DoctorSchedule::create([
                'doctor_id' => $doctor1->id,
                'day_of_week' => $day,
                'start_time' => '14:00',
                'end_time' => '18:00',
            ]);
        }

        // Doctor 2: Dra. Ana Rodríguez
        $doctor2 = Doctor::create([
            'name' => 'Dra. Ana Rodríguez',
            'slug' => 'dra-ana-rodriguez',
            'specialty' => 'Cirugía General',
            'bio' => 'Especialista en cirugía de vesícula, hernias y apendicitis. Más de 10 años de experiencia.',
            'email' => 'ana.rodriguez@hospital.com',
            'phone' => '+57 300 7654321',
            'is_active' => true,
        ]);

        // Horario: Lunes, Miércoles, Viernes 9:00 - 13:00
        foreach ([1, 3, 5] as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor2->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '13:00',
            ]);
        }

        // Doctor 3: Dr. Luis Gómez
        $doctor3 = Doctor::create([
            'name' => 'Dr. Luis Gómez',
            'slug' => 'dr-luis-gomez',
            'specialty' => 'Cirugía General',
            'bio' => 'Cirujano con especialización en cirugía oncológica y de trauma.',
            'email' => 'luis.gomez@hospital.com',
            'phone' => '+57 300 9876543',
            'is_active' => true,
        ]);

        // Horario: Martes y Jueves 10:00 - 16:00
        foreach ([2, 4] as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor3->id,
                'day_of_week' => $day,
                'start_time' => '10:00',
                'end_time' => '16:00',
            ]);
        }
    }
}